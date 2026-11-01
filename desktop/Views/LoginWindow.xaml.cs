using System.Windows;
using SkillSwap.Desktop.ViewModels;

namespace SkillSwap.Desktop.Views;

/// <summary>
/// LoginWindow — fenêtre d'authentification de l'administrateur (UC-20).
/// </summary>
public partial class LoginWindow : Window
{
    private readonly LoginViewModel _viewModel = new();

    public LoginWindow()
    {
        InitializeComponent();
        DataContext = _viewModel;
        _viewModel.ConnexionReussie += OuvrirFenetrePrincipale;
    }

    /// <summary>
    /// Le mot de passe est transmis en paramètre de commande plutôt que lié à
    /// une propriété du ViewModel : un PasswordBox n'expose volontairement pas
    /// sa valeur en liaison de données, pour éviter qu'elle ne réside en clair
    /// dans un objet observable (bonne pratique de sécurité, NFR-053).
    /// </summary>
    private void BoutonConnexion_Click(object sender, RoutedEventArgs e)
    {
        if (_viewModel.ConnexionCommand.CanExecute(ChampMotDePasse.Password))
        {
            _viewModel.ConnexionCommand.Execute(ChampMotDePasse.Password);
        }
    }

    private void OuvrirFenetrePrincipale(object? sender, EventArgs e)
    {
        var fenetrePrincipale = new MainWindow();
        Application.Current.MainWindow = fenetrePrincipale;
        fenetrePrincipale.Show();
        Close();
    }
}
