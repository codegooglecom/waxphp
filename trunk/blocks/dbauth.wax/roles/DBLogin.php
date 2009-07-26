<?php
	Wax::LoadBlock("database");
	
	interface DBAuth extends DBCRUD {}
	
	class DBAuthActions {
		function Authenticate(DBAuth $self, $username, $password) {
			$arguments = array(
				"username" => $username,
				"password" => MD5($password),
				"limit" => array(
					"count" => 1
				)
			);
			$result = $self->Read($arguments);
			if (!is_null($result)) return $result;
			else return FALSE;
		}
		function CheckUsername(DBAuth $self, $username) {
			$arguments = array(
				"username" => $username
			);
			$result = $self->Read($arguments);
			if (!is_null($result))
			 	return true;
			else
				return FALSE;
		}
		
		// create and delete users
		function CreateUser(DBAuth $self, $info) {
			return $self->Create($info);
		}
		function DeleteUser(DBAuth $self, $id) {
			$self->Delete($id);
		}
		
		// work with passwords
		function ChangePassword(DBAuth $self, $username, $old, $new, $newagain = NULL) {
			// if the program decides not to do a double check, fake it
			if (is_null($newagain))
				$newagain = $new;
			
			if ($info = $self->Authenticate($username,$old)) {
				// then it's ok -- perform the update
				$update = array("password" => md5($new), "id" => $info['id']);
				$self->Update($update);
				return true;
			}
			return false;
		}
		function ResetPassword(DBUser $self, $username) {
			$autogen = substr(md5(microtime(true)), 0, 8);
			$update = array("username" => $username, "password" => $autogen);
			$self->Update($update);
			return true;
		}
	}
?>