$files = Get-ChildItem -Path "js", "backend" -Recurse -Include *.js,*.php
foreach ($file in $files) {
    if ($file.FullName -match "vendor") { continue }
    $content = Get-Content $file.FullName
    $matches = $content | Select-String -Pattern "http://localhost", "var_dump", "print_r", "APIAutenticador"
    if ($matches) {
        Write-Host "File: $($file.Name)" -ForegroundColor Cyan
        $matches | ForEach-Object { Write-Host "  $($_)" }
    }
}
