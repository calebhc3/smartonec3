@echo off
cd /d C:\projetos\smartone
start "" /min cmd /k "php artisan serve"
timeout /t 5
start "" /min cmd /k "npm run dev"
timeout /t 5
start "" http://localhost:8000/admin/agendamentos

exit
