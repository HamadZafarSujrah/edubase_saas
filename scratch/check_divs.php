<?php
$html = file_get_contents('e:\Al-hikma\School Management System\Folder structure\edubase-saas\resources\views\livewire\student-admission.blade.php');
$html = preg_replace('/<style>.*?<\/style>/is', '', $html);
$lines = explode("\n", $html);
$indent = 0;
foreach($lines as $i => $line) {
    if(preg_match_all('/<div/' , $line, $matches1)) { $indent += count($matches1[0]); }
    if(preg_match_all('/<\/div/' , $line, $matches2)) { $indent -= count($matches2[0]); }
    if($indent < 0) { echo "Found extra closing div at line ".($i+1)."\n"; $indent = 0;}
}
echo "Final indent: $indent\n";
