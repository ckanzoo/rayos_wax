<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class UserController extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->call->model('UserModel');
    }

    public function index()
    {
        $page = $this->io->get('page') ?? 1;
        $q    = trim($this->io->get('q') ?? '');
        $limit = 10;

        $all = $this->UserModel->get_paginated($q, $limit, $page);

        $data['users'] = $all['records'];
        $total_rows    = $all['total_rows'];

        // Pagination setup
        $this->pagination->set_options([
            'first_link'     => '⏮ First',
            'last_link'      => 'Last ⏭',
            'next_link'      => 'Next →',
            'prev_link'      => '← Prev',
            'page_delimiter' => '&page='
        ]);
        $this->pagination->set_theme('tailwind');
        $this->pagination->initialize($total_rows, $limit, $page, site_url('/').'?q='.$q);

        $data['page'] = $this->pagination->paginate();
        $data['q']    = $q;

        $this->call->view('user/view', $data);
    }

    public function create() { /* keep same */ }
    public function update($id) { /* keep same */ }
    public function delete($id) { /* keep same */ }
}
