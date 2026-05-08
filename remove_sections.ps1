$content = Get-Content 'dashboard.php' -Raw
$content = $content -replace '(?s)<div class="smart-dashboard__quick-actions">.*?</div>\s*', ''
$content = $content -replace '(?s)<div class="smart-dashboard__insights">.*?</div>\s*', ''
Set-Content 'dashboard.php' -Value $content -NoNewline
Write-Host "Sections removed successfully"
