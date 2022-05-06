<?php
    namespace App\Models;
    use MF\Model\Model;

    class Usuario extends Model {
        private $id;
        private $nome;
        private $email;
        private $senha;

        public function __get($attr)
        {
            return $this->$attr;
        }

        public function __set($attr, $value)
        {
            $this->$attr = $value;
        }

        // Salvar
        public function salvar() {
            $query = "
                INSERT INTO usuarios(nome, email, senha) VALUES (:nome, :email, :senha)
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue('nome' , $this->__get('nome'));
            $stmt->bindValue('email' , $this->__get('email'));
            $stmt->bindValue('senha' , $this->__get('senha'));
            $stmt->execute();

            return $stmt;
        }

        // Validar
        public function Validar() {
            // echo strlen($this->__get('nome'));
            if (strlen($this->__get('nome')) < 3 or strlen($this->__get('email')) < 3 or strlen($this->__get('senha')) < 3) {
                return false;
            } else {
                return true;
            }
        }

        // Recuperar usuario por e-mail
        public function getUsuarioPorEmail() {
            $query = "
                SELECT email FROM usuarios WHERE email = :email
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue('email' , $this->__get('email'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
    }
?>