<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Задаёт пароль пользователю админки (создаёт пользователя, если его ещё нет).
 * Без кавычек и интерактивного ввода: удобно запускать из интерфейса хостинга.
 *
 *   php artisan tenir:set-password user@example.com НовыйПароль123
 */
class SetAdminPassword extends Command
{
    protected $signature = 'tenir:set-password {email : E-mail пользователя} {password : Новый пароль (от 8 символов)} {--name=Admin : Имя для нового пользователя}';

    protected $description = 'Задать пароль пользователю админки (создаёт пользователя, если его нет)';

    public function handle(): int
    {
        $password = (string) $this->argument('password');
        if (mb_strlen($password) < 8) {
            $this->error('Пароль должен быть не короче 8 символов.');

            return self::FAILURE;
        }

        $user = User::firstOrNew(['email' => (string) $this->argument('email')]);
        $created = ! $user->exists;
        if ($created) {
            $user->name = (string) $this->option('name');
        }
        $user->password = Hash::make($password);
        $user->save();

        $this->info(($created ? 'Пользователь создан: ' : 'Пароль обновлён: ').$user->email);

        return self::SUCCESS;
    }
}
