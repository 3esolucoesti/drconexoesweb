<?php
	/**
	* iddesentupidora
	*/
	class Permissions extends model{ 

		private $group;
		private $permissions;

		public function setGroup($id,$id_company){
			$this->group = $id;
			$this->permissions = array();

			$sql = $this->db->prepare("SELECT params FROM permission_groups WHERE id = :id AND ISNULL(deleted_at)  ");
			$sql->bindValue(':id',$id);
			$sql->execute();

			if($sql->rowCount() > 0){
				$row = $sql->fetch();

				if(empty($row['params'])){
					$row['params'] = '0';
				}

				$params = $row['params'];

				$sql = $this->db->prepare("SELECT name FROM permission_params WHERE id IN ($params) AND ISNULL(deleted_at) ");
				$sql->execute();

				if($sql->rowCount() > 0){
					foreach ($sql->fetchAll() as $item) {
						$this->permissions[] = $item['name'];
					}
				}
			}	
		}

		public function hasPermission($name){
			if(in_array($name, $this->permissions)){
				return true;
			}else{
				return false;
			}
		}

		public function getList($id_company){
			$array = array();

				$sql = $this->db->prepare("SELECT * FROM permission_params WHERE ISNULL(deleted_at) ");
				$sql->execute();


				if($sql->rowCount() > 0){
					$array = $sql->fetchAll();
				}

			return $array;
		}

		public function getid($id){
			$array = array();

			$sql = $this->db->prepare("SELECT * FROM permission_params WHERE id = :id ");
			$sql->bindValue(':id', $id);
			$sql->execute();

			if($sql->rowCount() > 0){
				$array = $sql->fetch();
			}

			return $array;
		}


		public function add($pname,$id_company,$descricion,$public_private,$lista_pages){
			$sql = $this->db->prepare("INSERT INTO permission_params SET name = :name, id_company = :id_company , name_apresentar = :name_apresentar, public_private = :public_private, page_reference = :page_reference  ");
			$sql->bindValue(':name', $pname);
			$sql->bindValue(':id_company', $id_company);
			$sql->bindValue(':name_apresentar', $descricion);
			$sql->bindValue(':public_private', $public_private);
			$sql->bindValue(':page_reference', $lista_pages);
			$sql->execute();
		}

		public function edit($descricion,$id,$public_private,$lista_pages){
			$sql = $this->db->prepare("UPDATE permission_params SET  name_apresentar = :name_apresentar , public_private = :public_private, page_reference = :page_reference   WHERE id = :id ");
			$sql->bindValue(':name_apresentar', $descricion);
			$sql->bindValue(':id', $id);
			$sql->bindValue(':public_private', $public_private);
			$sql->bindValue(':page_reference', $lista_pages);
			$sql->execute();
		}

		public function delete($id){
			$sql = $this->db->prepare("UPDATE permission_params SET deleted_at = NOW()  WHERE id = :id  ");
			$sql->bindValue(':id', $id);
			$sql->execute();
		}

		


		public function getGroupList($id_company){
			$array = array();

				$sql = $this->db->prepare("SELECT * FROM permission_groups WHERE ISNULL(deleted_at) ");
				$sql->execute();


				if($sql->rowCount() > 0){
					$array = $sql->fetchAll();
				}

			return $array;
		}

		public function getGroup($id,$id_company){
			$array = array();

				$sql = $this->db->prepare("SELECT * FROM permission_groups WHERE id = :id AND ISNULL(deleted_at) ");
				$sql->bindValue(':id', $id);
				$sql->execute();


				if($sql->rowCount() > 0){
					$array = $sql->fetch();
					$array['params'] = explode(',', $array['params']);
				}

			return $array;
		}

		public function addGroup($pname, $plist ,$id_company){
			$params = implode(',', $plist);
			$sql = $this->db->prepare("INSERT INTO permission_groups SET name = :name, id_company = :id_company, params = :params ");
			$sql->bindValue(':name', $pname);
			$sql->bindValue(':id_company', $id_company);
			$sql->bindValue(':params', $params);
			$sql->execute();
		}

		public function editGroup($pname, $plist , $id, $id_company){
			$params = implode(',', $plist);
			$sql = $this->db->prepare("UPDATE permission_groups SET name = :name, id_company = :id_company, params = :params WHERE id = :id ");
			$sql->bindValue(':name', $pname);
			$sql->bindValue(':id_company', $id_company);
			$sql->bindValue(':params', $params);
			$sql->bindValue(':id', $id);
			$sql->execute();
		}
		
		public function deleteGroup($id){
			$u = new Users();
			if($u->findUsersInGroup($id) == false){
				$sql = $this->db->prepare("UPDATE permission_groups SET deleted_at = NOW() WHERE id = :id  ");
				$sql->bindValue(':id', $id);
				$sql->execute();
			}
		}



	}

?>