<?php

    class EjemploModel extends PulseModel
    {
        public function getUserById($id)
        {
            $sql = "SELECT * FROM users WHERE id = :id";
            $this->bind(':id', $id);
            $this->execute();
            return $this->single();
        }

        public function getAllUsers()
        {
            $sql = "SELECT * FROM users";
            $this->execute();
            return $this->resultSet();
        }

        public function createUser($name, $email, $password)
        {
            $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
            $this->bind(':name', $name);
            $this->bind(':email', $email);
            $this->bind(':password', $password);
            $this->execute();
            return $this->rowCount();
        }
    }
