<?php
    namespace App\Models;
    use MF\Model\Model;

    class Usuario extends Model {
        private $id;
        private $nome;
        private $email;
        private $senha;
        private $foto_perfil;

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

        // Autenticar
        public function autenticar() {
            $query = "
                SELECT id, nome, email, senha FROM usuarios WHERE email = :email AND senha = :senha
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue('email' , $this->__get('email'));
            $stmt->bindValue('senha' , $this->__get('senha'));
            $stmt->execute();

            $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

            if(!empty($usuario['id']) && !empty($usuario['nome'])) {
                $this->__set('id' , $usuario['id']);
                $this->__set('nome' , $usuario['nome']);
            }
            
            return $this;

        }

        public function getAll() {
            $query = "
                SELECT 
                    u.id, u.nome, u.foto_perfil, u.email, (
                        SELECT 
                            count(*)
                        FROM
                            usuarios_seguidores as us
                        WHERE
                            us.id_usuario = :id AND us.id_usuario_seguindo = u.id
                    ) as seguindo_sn
                FROM 
                    usuarios as u
                WHERE u.nome like :pesquisa AND u.id != :id
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':pesquisa' , '%'.$this->__get('nome').'%');
            $stmt->bindValue(':id' , $this->__get('id'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function seguirUsuario($id_usuario) {
            $query = "
                INSERT INTO usuarios_seguidores(id_usuario , id_usuario_seguindo) VALUES(:id_usuario , :id_usuario_seguindo)
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario' , $this->__get('id'));
            $stmt->bindValue(':id_usuario_seguindo' , $id_usuario);
            $stmt->execute();

            return true;
        }

        public function deixarseguirUsuario($id_usuario) {
            $query = "
                DELETE FROM usuarios_seguidores WHERE id_usuario = :id_usuario AND id_usuario_seguindo = :id_usuario_seguindo
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario' , $this->__get('id'));
            $stmt->bindValue(':id_usuario_seguindo' , $id_usuario);
            $stmt->execute();

            return true;
        }
        
        public function infoUsuario()
        {
            $query = "
                SELECT nome FROM usuarios WHERE id = :id
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        public function totalTweets()
        {
            $query = "
                SELECT count(*) as total_tweets FROM tweets WHERE id_usuario = :id
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        public function totalSeguindo()
        {
            $query = "
                SELECT count(*) as total_seguindo FROM usuarios_seguidores WHERE id_usuario = :id
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        public function totalSeguidores()
        {
            $query = "
                SELECT count(*) as total_seguindo FROM usuarios_seguidores WHERE id_usuario_seguindo	 = :id
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        public function setFoto_perfil() {
            // echo $this->__get('foto_perfil');

            $query = "
                UPDATE 
                    usuarios
                SET
                    foto_perfil = :foto_perfil
                WHERE 
                    id = :id
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->bindValue(':foto_perfil', $this->__get('foto_perfil'));
            $stmt->execute();

            return true;
        }

        public function getFotoPerfil() {
            $query = "
                SELECT foto_perfil FROM usuarios WHERE id = :id
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        public function editar_perfil() {
            // echo 'chegamos aqui <br>';

            // echo $this->__get('email');
            // echo $this->__get('nome');
            // echo $this->__get('id');

            $query = "
                UPDATE 
                    usuarios
                SET
                    nome = :nome , email = :email
                WHERE 
                    id = :id
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':nome' , $this->__get('nome'));
            $stmt->bindValue(':email' , $this->__get('email'));
            $stmt->bindValue(':id' , $this->__get('id'));
            $stmt->execute();

            return true;
        }
    }
?>