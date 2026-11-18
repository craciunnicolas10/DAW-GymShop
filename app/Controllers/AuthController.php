<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Validator;
use App\Exceptions\ValidationException;
use App\Repositories\UserRepository;

/**
 * Login, registration and logout.
 */
final class AuthController extends Controller
{
    public function showLogin(): void
    {
        if ($this->auth->check()) {
            $this->redirect('/');
        }

        $this->render('auth/login', ['pageTitle' => 'Autentificare', 'errors' => [], 'old' => []]);
    }

    public function login(): void
    {
        $data = $this->request->all();

        try {
            (new Validator($data))
                ->required('email', 'Email')
                ->email('email', 'Email')
                ->required('password', 'Parolă')
                ->validate();
        } catch (ValidationException $exception) {
            $this->renderLoginForm($exception->getErrors(), $data);
            return;
        }

        $userRepository = new UserRepository(Database::getConnection());
        $user = $userRepository->findByEmail($data['email']);

        if ($user === null || !$user->verifyPassword($data['password'])) {
            $this->renderLoginForm(['email' => 'Adresa de email sau parola este incorectă.'], $data);
            return;
        }

        $this->auth->login($user);
        $this->session->flash('success', 'Bine ai revenit, ' . $user->getFullName() . '!');

        $intendedPath = $this->session->get('intendedPath');
        $this->session->remove('intendedPath');

        if (is_string($intendedPath) && str_starts_with($intendedPath, '/') && !str_starts_with($intendedPath, '//')) {
            $this->redirect($intendedPath);
        }

        $this->redirect($user->isAdmin() ? '/admin' : '/');
    }

    public function showRegister(): void
    {
        if ($this->auth->check()) {
            $this->redirect('/');
        }

        $this->render('auth/register', ['pageTitle' => 'Cont nou', 'errors' => [], 'old' => []]);
    }

    public function register(): void
    {
        $data = $this->request->all();
        $userRepository = new UserRepository(Database::getConnection());

        try {
            (new Validator($data))
                ->required('fullName', 'Nume complet')
                ->minLength('fullName', 'Nume complet', 3)
                ->maxLength('fullName', 'Nume complet', 100)
                ->required('email', 'Email')
                ->email('email', 'Email')
                ->maxLength('email', 'Email', 150)
                ->required('password', 'Parolă')
                ->minLength('password', 'Parolă', 8)
                ->pattern('password', '/^(?=.*[A-Za-z])(?=.*\d).+$/', 'Parola trebuie să conțină cel puțin o literă și o cifră.')
                ->required('passwordConfirmation', 'Confirmare parolă')
                ->matches('passwordConfirmation', 'password', 'Confirmarea parolei')
                ->validate();

            if ($userRepository->emailExists($data['email'])) {
                throw new ValidationException(['email' => 'Există deja un cont cu această adresă de email.']);
            }
        } catch (ValidationException $exception) {
            unset($data['password'], $data['passwordConfirmation']);
            $this->render('auth/register', [
                'pageTitle' => 'Cont nou',
                'errors' => $exception->getErrors(),
                'old' => $data,
            ], 422);
            return;
        }

        $userId = $userRepository->create(
            $data['fullName'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        );

        $this->auth->login($userRepository->findById($userId));
        $this->session->flash('success', 'Contul a fost creat. Bine ai venit în comunitatea GymShop!');
        $this->redirect('/');
    }

    public function logout(): void
    {
        $this->auth->logout();
        $this->session->flash('success', 'Te-ai deconectat cu succes.');
        $this->redirect('/');
    }

    private function renderLoginForm(array $errors, array $data): void
    {
        unset($data['password']);
        $this->render('auth/login', ['pageTitle' => 'Autentificare', 'errors' => $errors, 'old' => $data], 422);
    }
}
