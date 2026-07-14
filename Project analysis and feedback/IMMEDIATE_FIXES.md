# 🔧 EduBase SaaS - Immediate Fixes & Action Plan

## 🚨 CRITICAL FIXES - Do These TODAY

### Fix #1: Move Tenants Migration (5 minutes)

**Current Issue:**
```
❌ database/migrations/tenant/2024_01_01_000001_create_tenants_table.php
```

**Fix:**
```bash
# Step 1: Move the file
mv database/migrations/tenant/2024_01_01_000001_create_tenants_table.php \
   database/migrations/landlord/

# Step 2: Verify
ls -la database/migrations/landlord/
```

**Explanation:**
- Tenants table is a **landlord/central table**
- It manages ALL tenants
- Should NOT have tenant_id scoping
- Must exist BEFORE any tenant-specific tables

---

### Fix #2: Update Tenant Model (3 minutes)

**File:** `app/Models/Tenant.php`

**REMOVE this:**
```php
use App\Models\Traits\HasTenant;  // ❌ REMOVE

class Tenant extends Model {
    use HasFactory, HasTenant;  // ❌ REMOVE HasTenant
}
```

**CORRECT version:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;  // ✅ NO HasTenant!

    protected $fillable = [
        'name',
        'code',
        'subdomain',
        'domain',
        'database',
        'email',
        'address',
        'phone',
        'status',
        'logo_url',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Check if the tenant is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get all users belonging to this tenant.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all campuses belonging to this tenant.
     */
    public function campuses()
    {
        return $this->hasMany(\App\Models\Campus\Campus::class);
    }

    /**
     * Get all students belonging to this tenant.
     */
    public function students()
    {
        return $this->hasMany(\App\Models\Student\Student::class);
    }

    /**
     * Scope to get only active tenants.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
```

---

### Fix #3: Delete Duplicate User Model (2 minutes)

**Delete this file:**
```bash
rm app/Models/Tenant/User.php
```

**Keep only:**
```
app/Models/User.php  ✅ This is the correct one
```

---

### Fix #4: Add All Model Relationships (15 minutes)

#### Campus Model (Complete Version)

**File:** `app/Models/Campus/Campus.php`

```php
<?php

namespace App\Models\Campus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasTenant;

class Campus extends Model
{
    use HasFactory, HasTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'address',
        'contact_email',
        'contact_phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the tenant that owns this campus.
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }

    /**
     * Get all campus classes (pivot) for this campus.
     */
    public function campusClasses()
    {
        return $this->hasMany(\App\Models\Academic\CampusClass::class);
    }

    /**
     * Get all students enrolled in this campus.
     */
    public function students()
    {
        return $this->hasMany(\App\Models\Student\Student::class);
    }

    /**
     * Get all sessions associated with this campus.
     */
    public function sessions()
    {
        return $this->hasMany(\App\Models\Academic\Session::class);
    }

    /**
     * Scope to get only active campuses.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the number of students in this campus.
     */
    public function getStudentCountAttribute()
    {
        return $this->students()->count();
    }
}
```

#### Session Model (Complete Version)

**File:** `app/Models/Academic/Session.php`

```php
<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class Session extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id',
        'campus_id',
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the campus this session belongs to.
     */
    public function campus()
    {
        return $this->belongsTo(\App\Models\Campus\Campus::class);
    }

    /**
     * Get all students enrolled in this session.
     */
    public function students()
    {
        return $this->hasMany(\App\Models\Student\Student::class);
    }

    /**
     * Scope to get only active sessions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get current session.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_active', true)
                     ->where('start_date', '<=', now())
                     ->where('end_date', '>=', now());
    }
}
```

#### SchoolClass Model (Complete Version)

**File:** `app/Models/Academic/SchoolClass.php`

```php
<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class SchoolClass extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'numeric_value',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'numeric_value' => 'integer',
    ];

    /**
     * Get all sections for this class.
     */
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    /**
     * Get all campus assignments for this class.
     */
    public function campusClasses()
    {
        return $this->hasMany(CampusClass::class);
    }

    /**
     * Get all students in this class.
     */
    public function students()
    {
        return $this->hasMany(\App\Models\Student\Student::class);
    }

    /**
     * Get all subjects assigned to this class.
     */
    public function subjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'class_subjects',
            'school_class_id',
            'subject_id'
        );
    }

    /**
     * Scope to get only active classes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

#### Section Model (Complete Version)

**File:** `app/Models/Academic/Section.php`

```php
<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class Section extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id',
        'school_class_id',
        'campus_id',
        'name',
        'capacity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
    ];

    /**
     * Get the class this section belongs to.
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * Get the campus this section belongs to.
     */
    public function campus()
    {
        return $this->belongsTo(\App\Models\Campus\Campus::class);
    }

    /**
     * Get all students in this section.
     */
    public function students()
    {
        return $this->hasMany(\App\Models\Student\Student::class);
    }

    /**
     * Check if section is at capacity.
     */
    public function isAtCapacity(): bool
    {
        return $this->students()->count() >= $this->capacity;
    }

    /**
     * Get available seats.
     */
    public function getAvailableSeatsAttribute()
    {
        return max(0, $this->capacity - $this->students()->count());
    }

    /**
     * Scope to get only active sections.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

---

### Fix #5: Add Missing Indexes (10 minutes)

**Create new migration:**
```bash
php artisan make:migration add_performance_indexes_to_tenant_tables
```

**File:** `database/migrations/2026_04_19_XXXXXX_add_performance_indexes_to_tenant_tables.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Campuses table indexes
        Schema::table('campuses', function (Blueprint $table) {
            $table->index(['tenant_id', 'is_active'], 'idx_campuses_tenant_active');
        });

        // Sessions table indexes
        Schema::table('sessions', function (Blueprint $table) {
            $table->index(['tenant_id', 'is_active'], 'idx_sessions_tenant_active');
            $table->index(['tenant_id', 'campus_id'], 'idx_sessions_tenant_campus');
        });

        // School classes table indexes
        Schema::table('school_classes', function (Blueprint $table) {
            $table->index(['tenant_id', 'is_active'], 'idx_classes_tenant_active');
        });

        // Sections table indexes
        Schema::table('sections', function (Blueprint $table) {
            $table->index(['tenant_id', 'school_class_id'], 'idx_sections_tenant_class');
            $table->index(['tenant_id', 'campus_id'], 'idx_sections_tenant_campus');
            $table->index(['tenant_id', 'is_active'], 'idx_sections_tenant_active');
        });

        // Students table indexes
        Schema::table('students', function (Blueprint $table) {
            $table->index(['tenant_id', 'session_id'], 'idx_students_tenant_session');
            $table->index(['tenant_id', 'campus_id'], 'idx_students_tenant_campus');
            $table->index(['tenant_id', 'school_class_id'], 'idx_students_tenant_class');
            $table->index(['tenant_id', 'section_id'], 'idx_students_tenant_section');
            $table->index('admission_no', 'idx_students_admission_no');
            $table->index(['tenant_id', 'is_active'], 'idx_students_tenant_active');
        });
    }

    public function down()
    {
        Schema::table('campuses', function (Blueprint $table) {
            $table->dropIndex('idx_campuses_tenant_active');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex('idx_sessions_tenant_active');
            $table->dropIndex('idx_sessions_tenant_campus');
        });

        Schema::table('school_classes', function (Blueprint $table) {
            $table->dropIndex('idx_classes_tenant_active');
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->dropIndex('idx_sections_tenant_class');
            $table->dropIndex('idx_sections_tenant_campus');
            $table->dropIndex('idx_sections_tenant_active');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('idx_students_tenant_session');
            $table->dropIndex('idx_students_tenant_campus');
            $table->dropIndex('idx_students_tenant_class');
            $table->dropIndex('idx_students_tenant_section');
            $table->dropIndex('idx_students_admission_no');
            $table->dropIndex('idx_students_tenant_active');
        });
    }
};
```

---

## 🛠️ IMPROVED LIVEWIRE COMPONENTS

### Improved CampusManager Component

**File:** `app/Livewire/CampusManager.php`

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Campus\Campus;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;

class CampusManager extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    // Form properties
    #[Validate('required|string|min:3|max:255')]
    public $name = '';
    
    #[Validate('required|string|min:10|max:500')]
    public $address = '';
    
    #[Validate('nullable|email|max:255')]
    public $contact_email = '';
    
    #[Validate('nullable|regex:/^[0-9+\-\s()]+$/|max:20')]
    public $contact_phone = '';
    
    #[Validate('required|boolean')]
    public $is_active = true;

    // Component state
    public $campus_id;
    public $isModalOpen = false;
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    // Validation messages
    protected $messages = [
        'name.required' => 'Campus name is required',
        'name.min' => 'Campus name must be at least 3 characters',
        'address.required' => 'Campus address is required',
        'address.min' => 'Address must be at least 10 characters',
        'contact_email.email' => 'Please enter a valid email address',
        'contact_phone.regex' => 'Please enter a valid phone number',
    ];

    public function render()
    {
        return view('livewire.campus-manager', [
            'campuses' => Campus::query()
                ->withCount('students')  // Add student count
                ->when($this->search, function($query) {
                    $query->where(function($q) {
                        $q->where('name', 'like', "%{$this->search}%")
                          ->orWhere('address', 'like', "%{$this->search}%")
                          ->orWhere('contact_email', 'like', "%{$this->search}%");
                    });
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(10),
        ])->layout('layouts.app');
    }

    public function updatingSearch()
    {
        $this->resetPage();  // Reset to page 1 when searching
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
        $this->resetValidation();
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->address = '';
        $this->contact_email = '';
        $this->contact_phone = '';
        $this->is_active = true;
        $this->campus_id = null;
    }

    public function store()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            Campus::updateOrCreate(
                ['id' => $this->campus_id],
                [
                    'name' => $this->name,
                    'address' => $this->address,
                    'contact_email' => $this->contact_email,
                    'contact_phone' => $this->contact_phone,
                    'is_active' => $this->is_active,
                    // tenant_id is automatically added by HasTenant trait
                ]
            );

            DB::commit();

            session()->flash('success', 
                $this->campus_id ? 'Campus updated successfully.' : 'Campus created successfully.');

            $this->closeModal();
            $this->resetInputFields();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to save campus: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $campus = Campus::findOrFail($id);
            
            $this->campus_id = $id;
            $this->name = $campus->name;
            $this->address = $campus->address;
            $this->contact_email = $campus->contact_email;
            $this->contact_phone = $campus->contact_phone;
            $this->is_active = $campus->is_active;

            $this->openModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Campus not found.');
        }
    }

    public function delete($id)
    {
        try {
            $campus = Campus::findOrFail($id);
            
            // Check if campus has students
            if ($campus->students()->exists()) {
                session()->flash('error', 'Cannot delete campus with enrolled students. Please transfer students first.');
                return;
            }

            // Check if campus has sections
            if ($campus->sections()->exists()) {
                session()->flash('error', 'Cannot delete campus with sections. Please remove sections first.');
                return;
            }

            $campus->delete();
            session()->flash('success', 'Campus deleted successfully.');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete campus: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $campus = Campus::findOrFail($id);
            $campus->update(['is_active' => !$campus->is_active]);
            
            session()->flash('success', 
                $campus->is_active ? 'Campus activated.' : 'Campus deactivated.');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update campus status.');
        }
    }
}
```

---

## 🎯 IMMEDIATE ACTION CHECKLIST

### Today (2-3 hours)

- [ ] **Step 1:** Move tenants migration to landlord folder (5 min)
- [ ] **Step 2:** Update Tenant model - remove HasTenant (3 min)
- [ ] **Step 3:** Delete duplicate User model (2 min)
- [ ] **Step 4:** Add relationships to Campus model (5 min)
- [ ] **Step 5:** Add relationships to Session model (5 min)
- [ ] **Step 6:** Add relationships to SchoolClass model (5 min)
- [ ] **Step 7:** Add relationships to Section model (5 min)
- [ ] **Step 8:** Create and run indexes migration (10 min)
- [ ] **Step 9:** Update CampusManager component (15 min)
- [ ] **Step 10:** Test everything works (30 min)

### Testing Steps

```bash
# 1. Clear cache
php artisan cache:clear
php artisan config:clear

# 2. Run migrations
php artisan migrate:fresh

# 3. Create test tenant
php artisan tinker
>>> $tenant = \App\Models\Tenant::create([
...     'name' => 'Test School',
...     'subdomain' => 'testschool',
...     'status' => 'active'
... ]);

# 4. Set tenant in session
>>> session(['tenant_id' => $tenant->id]);

# 5. Create test campus
>>> $campus = \App\Models\Campus\Campus::create([
...     'name' => 'Main Campus',
...     'address' => '123 Test Street',
...     'is_active' => true
... ]);

# 6. Verify tenant_id was auto-added
>>> $campus->tenant_id  // Should equal $tenant->id

# 7. Test scoping works
>>> \App\Models\Campus\Campus::all();  // Should only show campuses for current tenant
```

---

## 🚀 After Fixes - Next Steps

Once all critical fixes are done:

### This Week:
1. ✅ Complete Module 2 (Sections, Classes)
2. ✅ Add middleware for tenant context
3. ✅ Add authorization policies

### Next Week:
4. ✅ Continue with Students module
5. ✅ Add fees module functionality
6. ✅ Create tenant switcher UI

---

## 📞 Need Help?

If you encounter issues:

1. **Database errors:** Check migration order
2. **Tenant scoping not working:** Verify session has tenant_id
3. **Relationships broken:** Check foreign key names match
4. **Livewire errors:** Clear cache and check syntax

---

**Ready to fix? Start with Step 1!** 🎯
