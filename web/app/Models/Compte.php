<?php

namespace App\Models;

use App\Core\Model;

class Compte extends Model
{
    protected string $table = 'compte';
    protected string $primaryKey = 'idCompte';

    public function findByEmail(string $email): ?array
    {
        return $this->first(['email' => $email]);
    }

    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    public function createWithProfile(array $compteData, array $profileData, string $type): int
    {
        $this->db->beginTransaction();
        try {
            $compteData['typeCompte'] = $type;
            $idCompte = $this->insert($compteData);

            $profileData['idCompte'] = $idCompte;
            $profileTable = $type === 'ADMIN' ? 'administrateur' : 'utilisateur';
            $columns = array_keys($profileData);
            $placeholders = array_map(fn($c) => ':' . $c, $columns);
            $sql = sprintf(
                'INSERT INTO %s (%s) VALUES (%s)',
                $profileTable,
                implode(', ', $columns),
                implode(', ', $placeholders)
            );
            $stmt = $this->db->prepare($sql);
            $stmt->execute($profileData);

            $this->db->commit();
            return $idCompte;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function findWithProfile($idCompte): ?array
    {
        $compte = $this->find($idCompte);
        if (!$compte) {
            return null;
        }
        $table = $compte['typeCompte'] === 'ADMIN' ? 'administrateur' : 'utilisateur';
        $stmt = $this->db->prepare("SELECT * FROM {$table} WHERE idCompte = :id");
        $stmt->execute(['id' => $idCompte]);
        $profile = $stmt->fetch();

        return $profile ? array_merge($compte, $profile) : $compte;
    }

    public function registerFailedAttempt(string $email): void
    {
        $key = 'login_attempts_' . md5($email);
        $_SESSION[$key] = ($_SESSION[$key] ?? 0) + 1;
        if ($_SESSION[$key] >= MAX_LOGIN_ATTEMPTS) {
            $_SESSION[$key . '_locked_until'] = time() + (LOCKOUT_DURATION_MIN * 60);
        }
    }

    public function isLocked(string $email): bool
    {
        $key = 'login_attempts_' . md5($email) . '_locked_until';
        return isset($_SESSION[$key]) && $_SESSION[$key] > time();
    }

    public function clearFailedAttempts(string $email): void
    {
        $base = 'login_attempts_' . md5($email);
        unset($_SESSION[$base], $_SESSION[$base . '_locked_until']);
    }

    public function updateStatut($idCompte, string $statut): bool
    {
        return $this->update($idCompte, ['statut' => $statut]);
    }
}
