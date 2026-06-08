Write-Host "=== juega123 PHP 8.x local check ===" -ForegroundColor Cyan
$project = "C:\xampp\htdocs\juega123"
$files = @(
  "$project\index.php",
  "$project\system.php",
  "$project\include\lib\php8_compat.php",
  "$project\System.php",
  "$project\include\class\dbconfig.php",
  "$project\include\gui\invitation_pool.inc.php",
  "$project\include\class\invitation_pool.php"
)
foreach($f in $files){ if(Test-Path $f){ Write-Host "OK: $f" -ForegroundColor Green } else { Write-Host "FALTA: $f" -ForegroundColor Red } }
Write-Host "`nVersiones:" -ForegroundColor Cyan
php -v
mysql --version
Write-Host "`nPuertos:" -ForegroundColor Cyan
Test-NetConnection localhost -Port 80
Test-NetConnection localhost -Port 3306
Write-Host "`nLint PHP principales:" -ForegroundColor Cyan
php -l "$project\index.php"
php -l "$project\system.php"
php -l "$project\include\class\dbconfig.php"
php -l "$project\include\class\invitation_pool.php"
php -l "$project\include\class\horse_event.php"
