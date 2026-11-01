using System.Windows;
using SkillSwap.Desktop.ViewModels;

namespace SkillSwap.Desktop.Views;

/// <summary>
/// MainWindow — fenêtre principale de l'espace d'administration.
/// </summary>
public partial class MainWindow : Window
{
    private readonly MainViewModel _viewModel = new();

    public MainWindow()
    {
        InitializeComponent();
        DataContext = _viewModel;
        _viewModel.DeconnexionDemandee += RetourEcranConnexion;
    }

    private void RetourEcranConnexion(object? sender, EventArgs e)
    {
        var fenetreConnexion = new LoginWindow();
        Application.Current.MainWindow = fenetreConnexion;
        fenetreConnexion.Show();
        Close();
    }
}
