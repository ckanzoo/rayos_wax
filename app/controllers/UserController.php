<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class UserController extends Controller {

    public function __construct()
    {
        parent::__construct();
    }

    
    public function index()
    {
        $data['users'] = $this->UserModel->all();
        $this->call->view('user/view', $data);
    }

    
    public function create()
    {
        if ($this->io->method() === 'post') {
            $username = $this->io->post('username');
            $email    = $this->io->post('email');

            if (!empty($username) && !empty($email)) {
                $this->UserModel->insert([
                    'username' => $username,
                    'email'    => $email
                ]);
                redirect('/'); 
            } else {
                $data['error'] = "All fields are required!";
                $this->call->view('user/create', $data);
            }
        } else {
            $this->call->view('user/create');
        }
    }

    
    public function update($id)
    {
        $user = $this->UserModel->find($id);

        if (!$user) {
            redirect('/'); 
        }

        if ($this->io->method() === 'post') {
            $username = $this->io->post('username');
            $email    = $this->io->post('email');

            if (!empty($username) && !empty($email)) {
                $this->UserModel->update($id, [
                    'username' => $username,
                    'email'    => $email
                ]);
                redirect('/'); 
            } else {
                $data['user'] = $user;
                $data['error'] = "All fields are required!";
                $this->call->view('user/update', $data);
            }
        } else {
            $this->call->view('user/update', ['user' => $user]);
        }
    }

    
    public function delete($id)
    {
        $this->UserModel->delete($id);
        redirect('/');
    }
}
