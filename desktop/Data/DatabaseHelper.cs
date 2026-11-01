using System.Configuration;
using System.Data;
using MySql.Data.MySqlClient;

namespace SkillSwap.Desktop.Data;

/// <summary>
/// DatabaseHelper — couche d'accès aux données ADO.NET.
/// Toutes les requêtes passent par des commandes paramétrées
/// (MySqlParameter), afin de prévenir toute injection SQL.
/// </summary>
public static class DatabaseHelper
{
    private static string ConnectionString =>
        ConfigurationManager.ConnectionStrings["SkillSwapDb"]?.ConnectionString
        ?? throw new InvalidOperationException(
            "La chaîne de connexion « SkillSwapDb » est introuvable dans App.config.");

    /// <summary>Ouvre une nouvelle connexion ADO.NET à la base SkillSwap.</summary>
    public static MySqlConnection CreateConnection()
    {
        var connection = new MySqlConnection(ConnectionString);
        connection.Open();
        return connection;
    }

    /// <summary>Vérifie que la base est accessible (utilisé au démarrage).</summary>
    public static bool TestConnection(out string message)
    {
        try
        {
            using var connection = CreateConnection();
            message = "Connexion à la base de données établie.";
            return true;
        }
        catch (Exception ex)
        {
            message = "Impossible de se connecter à la base de données : " + ex.Message;
            return false;
        }
    }

    /// <summary>Exécute une requête SELECT et projette chaque ligne via <paramref name="mapper"/>.</summary>
    public static List<T> Query<T>(
        string sql,
        Action<MySqlParameterCollection>? bind,
        Func<IDataRecord, T> mapper)
    {
        var results = new List<T>();

        using var connection = CreateConnection();
        using var command = new MySqlCommand(sql, connection);
        bind?.Invoke(command.Parameters);

        using var reader = command.ExecuteReader();
        while (reader.Read())
        {
            results.Add(mapper(reader));
        }
        return results;
    }

    /// <summary>Exécute une requête SELECT retournant une seule ligne (ou null).</summary>
    public static T? QuerySingle<T>(
        string sql,
        Action<MySqlParameterCollection>? bind,
        Func<IDataRecord, T> mapper) where T : class
    {
        return Query(sql, bind, mapper).FirstOrDefault();
    }

    /// <summary>Exécute une requête INSERT / UPDATE / DELETE et retourne le nombre de lignes affectées.</summary>
    public static int Execute(string sql, Action<MySqlParameterCollection>? bind)
    {
        using var connection = CreateConnection();
        using var command = new MySqlCommand(sql, connection);
        bind?.Invoke(command.Parameters);
        return command.ExecuteNonQuery();
    }

    /// <summary>Exécute une requête d'agrégation retournant une valeur scalaire entière.</summary>
    public static int ExecuteScalarInt(string sql, Action<MySqlParameterCollection>? bind = null)
    {
        using var connection = CreateConnection();
        using var command = new MySqlCommand(sql, connection);
        bind?.Invoke(command.Parameters);
        var result = command.ExecuteScalar();
        return result == null || result == DBNull.Value ? 0 : Convert.ToInt32(result);
    }

    /// <summary>
    /// Exécute plusieurs opérations dans une transaction unique.
    /// Garantit l'intégrité transactionnelle des opérations sensibles
    /// (ajustement de points, régénération de certificat) exigée par NFR-018.
    /// </summary>
    public static void ExecuteTransaction(Action<MySqlConnection, MySqlTransaction> work)
    {
        using var connection = CreateConnection();
        using var transaction = connection.BeginTransaction();
        try
        {
            work(connection, transaction);
            transaction.Commit();
        }
        catch
        {
            transaction.Rollback();
            throw;
        }
    }

    // ---------- Utilitaires de lecture tolérants aux valeurs NULL ----------
    public static string GetString(IDataRecord r, string col)
        => r[col] == DBNull.Value ? string.Empty : Convert.ToString(r[col]) ?? string.Empty;

    public static string? GetNullableString(IDataRecord r, string col)
        => r[col] == DBNull.Value ? null : Convert.ToString(r[col]);

    public static int GetInt(IDataRecord r, string col)
        => r[col] == DBNull.Value ? 0 : Convert.ToInt32(r[col]);

    public static int? GetNullableInt(IDataRecord r, string col)
        => r[col] == DBNull.Value ? null : Convert.ToInt32(r[col]);

    public static decimal? GetNullableDecimal(IDataRecord r, string col)
        => r[col] == DBNull.Value ? null : Convert.ToDecimal(r[col]);

    public static DateTime GetDateTime(IDataRecord r, string col)
        => r[col] == DBNull.Value ? DateTime.MinValue : Convert.ToDateTime(r[col]);

    public static DateTime? GetNullableDateTime(IDataRecord r, string col)
        => r[col] == DBNull.Value ? null : Convert.ToDateTime(r[col]);
}
