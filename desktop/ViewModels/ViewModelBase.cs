using System.ComponentModel;
using System.Runtime.CompilerServices;

namespace SkillSwap.Desktop.ViewModels;

/// <summary>
/// ViewModelBase — implémentation de INotifyPropertyChanged, socle du
/// patron MVVM imposé par le cahier des charges.
/// </summary>
public abstract class ViewModelBase : INotifyPropertyChanged
{
    public event PropertyChangedEventHandler? PropertyChanged;

    protected void OnPropertyChanged([CallerMemberName] string? propertyName = null)
        => PropertyChanged?.Invoke(this, new PropertyChangedEventArgs(propertyName));

    /// <summary>Affecte une valeur et notifie la vue si elle a changé.</summary>
    protected bool SetProperty<T>(ref T champ, T valeur, [CallerMemberName] string? propertyName = null)
    {
        if (EqualityComparer<T>.Default.Equals(champ, valeur))
        {
            return false;
        }
        champ = valeur;
        OnPropertyChanged(propertyName);
        return true;
    }
}
