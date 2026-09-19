<?php

class Usuario
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function registrar(string $nombre, string $email, string $password): bool
    {
        $nombre = trim($nombre);
        $email = trim($email);

        $this->validarLongitudes($nombre, $email, $password);

        // Valida los datos recibidos
        if ($nombre === '' || $email === '' || $password === '') {
            throw new InvalidArgumentException("Todos los campos son obligatorios.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("El email no es valido.");
        }

        // Verifica que el email no exista
        $consulta = $this->conexion->prepare(
            "SELECT id FROM usuarios WHERE email = ?"
        );

        $consulta->execute([$email]);

        if ($consulta->fetch()) {
            throw new RuntimeException("El email ya esta registrado.");
        }

        // Genera el hash de la contraseña
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $consulta = $this->conexion->prepare(
            "INSERT INTO usuarios (nombre, email, password_hash)
             VALUES (?, ?, ?)"
        );

        return $this->ejecutarEscritura($consulta, [
            $nombre,
            $email,
            $passwordHash
        ]);
    }

    public function obtenerPorId(int $id): ?array
    {
        // Busca los datos actuales del usuario
        $consulta = $this->conexion->prepare(
            "SELECT id, nombre, email, creado_en
             FROM usuarios
             WHERE id = ?"
        );

        $consulta->execute([$id]);

        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    public function actualizarPerfil(
        int $id,
        string $nombre,
        string $email,
        string $password = ''
    ): bool {
        $nombre = trim($nombre);
        $email = trim($email);

        $this->validarLongitudes($nombre, $email, $password);
        if (!$this->obtenerPorId($id)) {
            throw new RuntimeException("Usuario no encontrado.");
        }

        // Valida nombre y email
        if ($nombre === '' || $email === '') {
            throw new InvalidArgumentException("Nombre y email son obligatorios.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("El email no es valido.");
        }

        // Verifica que otro usuario no use el email
        $consulta = $this->conexion->prepare(
            "SELECT id FROM usuarios
             WHERE email = ? AND id <> ?"
        );

        $consulta->execute([$email, $id]);

        if ($consulta->fetch()) {
            throw new RuntimeException("El email ya esta registrado.");
        }

        // Actualiza la contraseña solamente si fue enviada
        if ($password !== '') {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $consulta = $this->conexion->prepare(
                "UPDATE usuarios
                 SET nombre = ?, email = ?, password_hash = ?
                 WHERE id = ?"
            );

            return $this->ejecutarEscritura($consulta, [
                $nombre,
                $email,
                $passwordHash,
                $id
            ]);
        }

        $consulta = $this->conexion->prepare(
            "UPDATE usuarios
             SET nombre = ?, email = ?
             WHERE id = ?"
        );

        return $this->ejecutarEscritura($consulta, [
            $nombre,
            $email,
            $id
        ]);
    }

    private function validarLongitudes(string $nombre, string $email, string $password): void
    {
        if (preg_match_all('/./us', $nombre) > 100 || strlen($email) > 255) {
            throw new InvalidArgumentException('Nombre: máximo 100 caracteres; email: máximo 255.');
        }
        if ($password !== '' && (strlen($password) < 8 || strlen($password) > 72)) {
            throw new InvalidArgumentException('La contraseña debe tener entre 8 y 72 bytes.');
        }
    }

    private function ejecutarEscritura(PDOStatement $consulta, array $parametros): bool
    {
        try {
            return $consulta->execute($parametros);
        } catch (PDOException $e) {
            if (in_array((int) ($e->errorInfo[1] ?? 0), [2601, 2627], true)) {
                throw new RuntimeException('El email ya esta registrado.', 0, $e);
            }
            throw $e;
        }
    }
}
