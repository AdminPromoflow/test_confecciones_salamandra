<?php

    class Model
    {
        protected $db;

        public function __construct()
        {
            $this->db = new PulseDatabase();
        }

        public function query($sql)
        {
            $this->db->query($sql);
        }

        public function bind($param, $value, $type = null)
        {
            $this->db->bind($param, $value, $type);
        }

        public function execute()
        {
            return $this->db->execute();
        }

        public function resultSet()
        {
            return $this->db->resultSet();
        }

        public function single()
        {
            return $this->db->single();
        }

        public function rowCount()
        {
            return $this->db->rowCount();
        }
    }
