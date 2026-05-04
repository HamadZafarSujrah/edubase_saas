<?php
$html = file_get_contents('e:\Al-hikma\School Management System\Folder structure\edubase-saas\resources\views\livewire\student-admission.blade.php');
$lines = explode("\n", $html);
$indent = 0;
foreach ($lines as $i => $line) {
    preg_match_all('/<div\b[^>]*>/', $line, $op);
    preg_match_all('/<\/div>/', $line, $cl);
    
    $c_op = count($op[0]);
    $c_cl = count($cl[0]);
    
    if ($c_op > 0 || $c_cl > 0) {
        $prev_indent = $indent;
        $indent += $c_op - $c_cl;
        printf("%3d | In: %2d | Op: %d | Cl: %d | %s\n", $i+1, $prev_indent, $c_op, $c_cl, trim($line));
    }
}
echo "FINAL INDENT ALGEBRA: " . $indent . "\n";
