<?php
require("Toro.php");

class FacturaHandler{
	function get($name=null) {
		try {
			$dbh = new PDO('sqlite:database.db');
		} catch (Exception $e) {
			die("Unable to connect: " . $e->getMessage());
		}
		try {
			if ($name!=null) {
				$stmt = $dbh->prepare("SELECT * FROM factura WHERE id = :name");
				$stmt->bindParam(':name', $name, PDO::PARAM_STR);
			} else {
				$stmt = $dbh->prepare("SELECT * FROM factura");
			}
			$stmt->execute();

			$data = Array();
			while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$data[] = $result;
			}
			echo json_encode($data);
		} catch (Exception $e) {
			echo "Failed: " . $e->getMessage();
		}
	}

	function post($name=null) {
		try {
			$dbh = new PDO('sqlite:database.db');
		} catch (Exception $e) {
			die("Unable to connect: " . $e->getMessage());
		}
		try {
			if ($name!=null) {
				$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$result = $dbh->query('Select * from producto where factura = ' . $name);

				$total = 0;
				foreach($result as $row){
					$total = $row['subtotal'];
				}

				$imp = ($total * 0.13);
				$total_f = $total + $imp;

				$stmt = $dbh->prepare("UPDATE factura SET imp=:imp, total_f=:total_f
					WHERE id = :name");
					$stmt->bindParam(':imp', $imp);
					$stmt->bindParam(':total_f', $total_f);
					$stmt->bindParam(':name', $name);

					$dbh->beginTransaction();
					$stmt->execute();
					$dbh->commit();
					echo 'Successfull';
				}
				else{
					$name = $_POST['number'];
					$area = $_POST['cliente'];
					$fecha = $_POST['fecha'];

					$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

					$stmt = $dbh->prepare("UPDATE factura SET cliente=:cliente, fecha=:fecha
						WHERE id = :name");
						$stmt->bindParam(':cliente', $area);
						$stmt->bindParam(':fecha', $fecha);
						$stmt->bindParam(':name', $name);

						$dbh->beginTransaction();
						$stmt->execute();
						$dbh->commit();
						echo 'Successfull';
					}
				} catch (Exception $e) {
					$dbh->rollBack();
					echo "Failed: " . $e->getMessage();
				}
			}

			function put() {
				try {
					$dbh = new PDO('sqlite:database.db');
				} catch (Exception $e) {
					die("Unable to connect: " . $e->getMessage());
				}
				try {
					$_REQUEST = [];
					if ($_SERVER['REQUEST_METHOD'] == 'PUT')
					{
						parse_str(file_get_contents("php://input"), $_PUT);

						foreach ($_PUT as $key => $valor)
						{
							unset($_PUT[$key]);

							$_PUT[str_replace('amp;', '', $key)] = $valor;
						}

						$_REQUEST = array_merge($_REQUEST, $_PUT);
					}

					$id = $_REQUEST['number'];
					$fecha = $_REQUEST['fecha'];
					$cliente = $_REQUEST['cliente'];
					$zero = 0;

					$stmt = $dbh->prepare("insert into factura (id, fecha, cliente, imp, total_f)
					values (:id,:fecha,:cliente,:imp,:total_f)");

					$stmt->bindParam(':id',$id);
					$stmt->bindParam(':fecha',$fecha);
					$stmt->bindParam(':cliente',$cliente);
					$stmt->bindParam(':imp',$zero);
					$stmt->bindParam(':total_f',$zero);

					$dbh->beginTransaction();
					$stmt->execute();
					$dbh->commit();
					echo 'Successfull';
				} catch (Exception $e) {
					$dbh->rollBack();
					echo "Failed: " . $e->getMessage();
				}
			}

			function delete($name=null){
				try {
					$dbh = new PDO('sqlite:database.db');
				} catch (Exception $e) {
					die("Unable to connect: " . $e->getMessage());
				}
				try {
					$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

					$stmt = $dbh->prepare("delete from factura
					WHERE id = :name");
					$stmt->bindParam(':name', $name);

					$dbh->beginTransaction();
					$stmt->execute();
					$dbh->commit();
					echo 'Successfull';
				} catch (Exception $e) {
					$dbh->rollBack();
					echo "Failed: " . $e->getMessage();
				}
			}
		}

		class ProductoHandler{
			function get($name=null) {
				try {
					$dbh = new PDO('sqlite:database.db');
				} catch (Exception $e) {
					die("Unable to connect: " . $e->getMessage());
				}
				try {
					if ($name!=null) {
						$stmt = $dbh->prepare("SELECT * FROM producto WHERE factura = :name");
						$stmt->bindParam(':name', $name, PDO::PARAM_STR);
					} else {
						$stmt = $dbh->prepare("SELECT * FROM producto");
					}
					$stmt->execute();

					$data = Array();
					while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
						$data[] = $result;
					}
					echo json_encode($data);
				} catch (Exception $e) {
					echo "Failed: " . $e->getMessage();
				}
			}
			function put() {
				try {
					$dbh = new PDO('sqlite:database.db');
				} catch (Exception $e) {
					die("Unable to connect: " . $e->getMessage());
				}
				try {
					$_REQUEST = [];
					if ($_SERVER['REQUEST_METHOD'] == 'PUT')
					{
						parse_str(file_get_contents("php://input"), $_PUT);

						foreach ($_PUT as $key => $valor)
						{
							unset($_PUT[$key]);

							$_PUT[str_replace('amp;', '', $key)] = $valor;
						}

						$_REQUEST = array_merge($_REQUEST, $_PUT);
					}
					$factura = $_REQUEST['factura'];
					$imp = $_REQUEST['qty'];
					$descripcion = $_REQUEST['descripcion'];
					$valor = $_REQUEST['valor'];
					$total_f = ($imp * $valor);

					$stmt = $dbh->prepare("INSERT INTO producto (cantidad, descripcion, valor, subtotal, factura)
					VALUES (:cantidad, :descripcion, :valor, :subtotal, :factura)");

					$stmt->bindParam(':factura',$factura);
					$stmt->bindParam(':cantidad',$imp);
					$stmt->bindParam(':descripcion',$descripcion);
					$stmt->bindParam(':valor',$valor);
					$stmt->bindParam(':subtotal',$total_f);

					$dbh->beginTransaction();
					$stmt->execute();
					$dbh->commit();
					echo 'Successfull';
				} catch (Exception $e) {
					$dbh->rollBack();
					echo "Failed: " . $e->getMessage();
				}
			}

			function delete($name=null){
				try {
					$dbh = new PDO('sqlite:database.db');
				} catch (Exception $e) {
					die("Unable to connect: " . $e->getMessage());
				}
				try {
					$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

					$stmt = $dbh->prepare("delete from producto
					WHERE id = :name");
					$stmt->bindParam(':name', $name);

					$dbh->beginTransaction();
					$stmt->execute();
					$dbh->commit();
					echo 'Successfull';
				} catch (Exception $e) {
					$dbh->rollBack();
					echo "Failed: " . $e->getMessage();
				}
			}
		}

		Toro::serve(array(
			"/factura" => "FacturaHandler",
			"/factura/:alpha" => "FacturaHandler",
			"/producto" => "ProductoHandler",
			"/producto/:alpha" => "ProductoHandler",
		));
		?>
