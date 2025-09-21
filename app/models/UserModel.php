<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class UserModel extends Model {
    protected $table = 'users';
    protected $primary_key = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_paginated($q = '', $limit = 10, $page = 1) {
        $query = $this->db->table($this->table);

        if (!empty($q)) {
            $query->group_start()
                  ->like('username', $q)
                  ->or_like('email', $q)
                  ->group_end();
        }

        // Count total
        $countQuery = clone $query;
        $total_rows = $countQuery->select_count('*', 'count')->get()['count'];

        // Paginated data
        $records = $query->order_by('id', 'ASC')
                         ->pagination($limit, $page)
                         ->get_all();

        return [
            'records'    => $records,
            'total_rows' => $total_rows
        ];
    }
}
