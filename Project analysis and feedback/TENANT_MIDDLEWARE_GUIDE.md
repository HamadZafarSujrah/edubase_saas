# 🔐 Multi-Tenancy Best Practices & Middleware

## Complete Tenant Context System

### 1. Create Tenant Middleware

**File:** `app/Http/Middleware/EnsureTenantContext.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class EnsureTenantContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return $next($request);
        }

        // For super admins, allow manual tenant switching
        if (auth()->user()->isSuperAdmin()) {
            if (!session()->has('tenant_id')) {
                // Redirect to tenant selection page
                if (!$request->is('select-tenant') && !$request->is('switch-tenant/*')) {
                    return redirect()->route('select-tenant');
                }
            }
        } else {
            // For regular users, set tenant from their user record
            if (!session()->has('tenant_id') && auth()->user()->tenant_id) {
                session(['tenant_id' => auth()->user()->tenant_id]);
            }
        }

        // Verify tenant exists and is active
        if (session()->has('tenant_id')) {
            $tenant = Tenant::find(session('tenant_id'));
            
            if (!$tenant) {
                session()->forget('tenant_id');
                Log::warning('Invalid tenant_id in session', [
                    'tenant_id' => session('tenant_id'),
                    'user_id' => auth()->id()
                ]);
                return redirect()->route('select-tenant')
                    ->with('error', 'Invalid tenant. Please select again.');
            }

            if (!$tenant->isActive()) {
                session()->forget('tenant_id');
                return redirect()->route('login')
                    ->with('error', 'This institution is currently inactive.');
            }

            // Make tenant available globally
            config(['app.current_tenant' => $tenant]);
            view()->share('currentTenant', $tenant);
        }

        return $next($request);
    }
}
```

### 2. Register Middleware

**File:** `app/Http/Kernel.php`

```php
protected $routeMiddleware = [
    // ... existing middleware
    'tenant' => \App\Http\Middleware\EnsureTenantContext::class,
];
```

### 3. Apply Middleware to Routes

**File:** `routes/web.php`

```php
// Routes that require tenant context
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/campus-manager', CampusManager::class)->name('campus.manager');
    Route::get('/students', StudentDirectory::class)->name('students.directory');
    // ... all tenant-scoped routes
});

// Tenant selection (super admin only)
Route::middleware(['auth'])->group(function () {
    Route::get('/select-tenant', SelectTenant::class)->name('select-tenant');
    Route::get('/switch-tenant/{id}', [TenantController::class, 'switch'])
        ->name('switch.tenant');
});
```

---

## 🎨 Tenant Selection UI

### Create Livewire Component

**File:** `app/Livewire/SelectTenant.php`

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tenant;

class SelectTenant extends Component
{
    public $tenants;
    public $selectedTenantId;

    public function mount()
    {
        // Only super admins can select tenants
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        $this->tenants = Tenant::active()->orderBy('name')->get();
        $this->selectedTenantId = session('tenant_id');
    }

    public function selectTenant($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        
        if (!$tenant->isActive()) {
            session()->flash('error', 'Selected institution is inactive.');
            return;
        }

        session(['tenant_id' => $tenantId]);
        
        return redirect()->route('dashboard')
            ->with('success', "Switched to {$tenant->name}");
    }

    public function render()
    {
        return view('livewire.select-tenant')->layout('layouts.guest');
    }
}
```

**View:** `resources/views/livewire/select-tenant.blade.php`

```html
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Select Institution</h4>
                </div>
                <div class="card-body">
                    @if (session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="list-group">
                        @forelse($tenants as $tenant)
                            <a href="#" 
                               wire:click.prevent="selectTenant({{ $tenant->id }})"
                               class="list-group-item list-group-item-action {{ $selectedTenantId == $tenant->id ? 'active' : '' }}">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">{{ $tenant->name }}</h5>
                                    @if($tenant->subdomain)
                                        <small>{{ $tenant->subdomain }}</small>
                                    @endif
                                </div>
                                @if($tenant->address)
                                    <p class="mb-1">{{ $tenant->address }}</p>
                                @endif
                                @if($selectedTenantId == $tenant->id)
                                    <small class="text-white">Currently Selected</small>
                                @endif
                            </a>
                        @empty
                            <div class="alert alert-info">
                                No institutions available.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
```

---

## 🔄 Tenant Switcher Component (Top Nav)

**File:** `app/Livewire/TenantSwitcher.php`

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tenant;

class TenantSwitcher extends Component
{
    public $currentTenant;
    public $tenants;
    public $showDropdown = false;

    public function mount()
    {
        if (auth()->user()->isSuperAdmin()) {
            $this->tenants = Tenant::active()->orderBy('name')->get();
            if (session()->has('tenant_id')) {
                $this->currentTenant = Tenant::find(session('tenant_id'));
            }
        }
    }

    public function switchTo($tenantId)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $tenant = Tenant::findOrFail($tenantId);
        session(['tenant_id' => $tenantId]);
        
        $this->currentTenant = $tenant;
        $this->showDropdown = false;
        
        $this->dispatch('tenant-switched');
        return redirect()->route('dashboard')
            ->with('success', "Switched to {$tenant->name}");
    }

    public function render()
    {
        return view('livewire.tenant-switcher');
    }
}
```

**View:** `resources/views/livewire/tenant-switcher.blade.php`

```html
@if(auth()->user()->isSuperAdmin())
    <div class="dropdown" x-data="{ open: false }">
        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                type="button" 
                @click="open = !open"
                @click.away="open = false">
            <i class="fas fa-building"></i>
            {{ $currentTenant ? $currentTenant->name : 'Select Institution' }}
        </button>
        
        <div class="dropdown-menu" 
             :class="{ 'show': open }" 
             style="max-height: 400px; overflow-y: auto;">
            @foreach($tenants as $tenant)
                <a class="dropdown-item {{ $currentTenant && $currentTenant->id == $tenant->id ? 'active' : '' }}" 
                   href="#"
                   wire:click.prevent="switchTo({{ $tenant->id }})">
                    {{ $tenant->name }}
                    @if($currentTenant && $currentTenant->id == $tenant->id)
                        <i class="fas fa-check float-right"></i>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
@endif
```

---

## 🛡️ Authorization Policies

### Campus Policy

**File:** `app/Policies/CampusPolicy.php`

```php
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Campus\Campus;

class CampusPolicy
{
    /**
     * Determine if the user can view any campuses.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view
    }

    /**
     * Determine if the user can view the campus.
     */
    public function view(User $user, Campus $campus): bool
    {
        // Can only view if belongs to same tenant
        return $user->tenant_id === $campus->tenant_id;
    }

    /**
     * Determine if the user can create campuses.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['Super Admin', 'Admin', 'Director']);
    }

    /**
     * Determine if the user can update the campus.
     */
    public function update(User $user, Campus $campus): bool
    {
        return $user->tenant_id === $campus->tenant_id && 
               in_array($user->role, ['Super Admin', 'Admin', 'Director']);
    }

    /**
     * Determine if the user can delete the campus.
     */
    public function delete(User $user, Campus $campus): bool
    {
        return $user->tenant_id === $campus->tenant_id && 
               $user->role === 'Super Admin';
    }
}
```

### Register Policy

**File:** `app/Providers/AuthServiceProvider.php`

```php
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Campus\Campus;
use App\Policies\CampusPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Campus::class => CampusPolicy::class,
        // Add more policies here
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
```

### Use Policy in Livewire

```php
public function delete($id)
{
    $campus = Campus::findOrFail($id);
    
    // Check authorization
    $this->authorize('delete', $campus);
    
    try {
        if ($campus->students()->exists()) {
            session()->flash('error', 'Cannot delete campus with students.');
            return;
        }
        
        $campus->delete();
        session()->flash('success', 'Campus deleted successfully.');
        
    } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        session()->flash('error', 'You are not authorized to delete this campus.');
    } catch (\Exception $e) {
        session()->flash('error', 'Failed to delete campus.');
    }
}
```

---

## 🧪 Testing Multi-Tenancy

### Create Test

**File:** `tests/Feature/MultiTenancyTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Campus\Campus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MultiTenancyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function tenant_scope_filters_campuses_correctly()
    {
        // Create two tenants
        $tenant1 = Tenant::create(['name' => 'School A', 'subdomain' => 'schoola', 'status' => 'active']);
        $tenant2 = Tenant::create(['name' => 'School B', 'subdomain' => 'schoolb', 'status' => 'active']);

        // Create campuses for each tenant
        session(['tenant_id' => $tenant1->id]);
        $campus1 = Campus::create(['name' => 'Campus A1', 'address' => 'Address A1', 'is_active' => true]);

        session(['tenant_id' => $tenant2->id]);
        $campus2 = Campus::create(['name' => 'Campus B1', 'address' => 'Address B1', 'is_active' => true]);

        // Set tenant 1 context
        session(['tenant_id' => $tenant1->id]);
        
        // Should only see tenant 1 campuses
        $campuses = Campus::all();
        $this->assertCount(1, $campuses);
        $this->assertEquals('Campus A1', $campuses->first()->name);

        // Switch to tenant 2
        session(['tenant_id' => $tenant2->id]);
        
        // Should only see tenant 2 campuses
        $campuses = Campus::all();
        $this->assertCount(1, $campuses);
        $this->assertEquals('Campus B1', $campuses->first()->name);
    }

    /** @test */
    public function tenant_id_is_automatically_added_when_creating()
    {
        $tenant = Tenant::create(['name' => 'Test School', 'subdomain' => 'test', 'status' => 'active']);
        
        session(['tenant_id' => $tenant->id]);
        
        $campus = Campus::create([
            'name' => 'Main Campus',
            'address' => '123 Test St',
            'is_active' => true,
        ]);

        $this->assertEquals($tenant->id, $campus->tenant_id);
    }

    /** @test */
    public function super_admin_can_switch_tenants()
    {
        $tenant1 = Tenant::create(['name' => 'School A', 'subdomain' => 'schoola', 'status' => 'active']);
        $tenant2 = Tenant::create(['name' => 'School B', 'subdomain' => 'schoolb', 'status' => 'active']);

        $superAdmin = User::factory()->create([
            'role' => 'Super Admin',
            'tenant_id' => $tenant1->id,
        ]);

        $this->actingAs($superAdmin);

        // Switch to tenant 2
        $response = $this->get(route('switch.tenant', $tenant2->id));

        $this->assertEquals($tenant2->id, session('tenant_id'));
    }
}
```

### Run Tests

```bash
php artisan test --filter MultiTenancyTest
```

---

## 📊 Tenant Statistics Helper

**File:** `app/Services/TenantStatsService.php`

```php
<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\Campus\Campus;
use App\Models\Student\Student;
use Illuminate\Support\Facades\DB;

class TenantStatsService
{
    public function getDashboardStats(?int $tenantId = null): array
    {
        $tenantId = $tenantId ?? session('tenant_id');

        return [
            'total_students' => Student::where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->count(),
                
            'total_campuses' => Campus::where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->count(),
                
            'total_sections' => DB::table('sections')
                ->where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->count(),
                
            'total_staff' => DB::table('users')
                ->where('tenant_id', $tenantId)
                ->where('status', 'active')
                ->count(),
                
            'students_by_campus' => Campus::where('tenant_id', $tenantId)
                ->withCount('students')
                ->get()
                ->pluck('students_count', 'name'),
                
            'students_by_class' => DB::table('students')
                ->join('school_classes', 'students.school_class_id', '=', 'school_classes.id')
                ->where('students.tenant_id', $tenantId)
                ->where('students.is_active', true)
                ->select('school_classes.name', DB::raw('count(*) as count'))
                ->groupBy('school_classes.name')
                ->pluck('count', 'name'),
        ];
    }
}
```

**Usage in Livewire:**

```php
// app/Livewire/Dashboard.php
public function mount(TenantStatsService $statsService)
{
    $this->stats = $statsService->getDashboardStats();
}
```

---

## 🎯 Summary

### What You Now Have:

1. ✅ **Tenant Middleware** - Ensures tenant context is always set
2. ✅ **Tenant Switcher** - UI component for super admins
3. ✅ **Authorization Policies** - Control who can do what
4. ✅ **Testing Framework** - Verify multi-tenancy works
5. ✅ **Stats Service** - Get tenant-specific statistics

### Integration Steps:

1. Create the middleware file
2. Register in Kernel.php
3. Add to route groups
4. Create tenant switcher component
5. Add to navigation
6. Create and register policies
7. Write tests

---

**Your multi-tenancy system will be rock-solid!** 🚀
