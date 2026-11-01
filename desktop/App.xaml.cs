using System.Windows;
using SkillSwap.Desktop.Data;

namespace SkillSwap.Desktop;

/// <summary>
/// App — point d'entrée de l'application bureautique d'administration SkillSwap.
/// </summary>
public partial class App : Application
{
    protected override void OnStartup(StartupEventArgs e)
    {
        base.OnStartup(e);

        // Vérification de l'accès à la base partagée avec l'application web,
        // afin d'afficher un message clair plutôt qu'une erreur technique (NFR-087).
        if (!DatabaseHelper.TestConnection(out var message))
        {
            MessageBox.Show(
                message + "\n\nVérifiez que le serveur MySQL/MariaDB (XAMPP) est démarré "
                        + "et que la chaîne de connexion de App.config est correcte.",
                "SkillSwap — Connexion impossible",
                MessageBoxButton.OK,
                MessageBoxImage.Error);
        }
    }
}
