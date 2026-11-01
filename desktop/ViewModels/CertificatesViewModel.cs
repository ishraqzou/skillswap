using System.Collections.ObjectModel;
using System.Windows.Input;
using SkillSwap.Desktop.Data;
using SkillSwap.Desktop.Models;
using SkillSwap.Desktop.Services;

namespace SkillSwap.Desktop.ViewModels;

/// <summary>
/// CertificatesViewModel — révocation et régénération des certificats (UC-27).
/// </summary>
public class CertificatesViewModel : ViewModelBase
{
    private readonly CertificatRepository _certificatRepository = new();
    private readonly CertificatService _certificatService = new();

    private Certificat? _certificatSelectionne;
    private string _recherche = string.Empty;
    private string _message = string.Empty;
    private bool _messageEstErreur;

    public ObservableCollection<Certificat> Certificats { get; } = new();

    public Certificat? CertificatSelectionne
    {
        get => _certificatSelectionne;
        set
        {
            if (SetProperty(ref _certificatSelectionne, value))
            {
                OnPropertyChanged(nameof(UnCertificatEstSelectionne));
            }
        }
    }

    public bool UnCertificatEstSelectionne => CertificatSelectionne != null;

    public string Recherche
    {
        get => _recherche;
        set => SetProperty(ref _recherche, value);
    }

    public string Message
    {
        get => _message;
        set { SetProperty(ref _message, value); OnPropertyChanged(nameof(ADesMessages)); }
    }

    public bool MessageEstErreur
    {
        get => _messageEstErreur;
        set => SetProperty(ref _messageEstErreur, value);
    }

    public bool ADesMessages => !string.IsNullOrEmpty(Message);

    public ICommand RafraichirCommand { get; }
    public ICommand RechercherCommand { get; }
    public ICommand RevoquerCommand { get; }
    public ICommand RegenererCommand { get; }

    public CertificatesViewModel()
    {
        RafraichirCommand = new RelayCommand(Charger);
        RechercherCommand = new RelayCommand(Rechercher);
        RevoquerCommand   = new RelayCommand(Revoquer,  () => UnCertificatEstSelectionne);
        RegenererCommand  = new RelayCommand(Regenerer, () => UnCertificatEstSelectionne);
        Charger();
    }

    public void Charger()
    {
        try
        {
            Certificats.Clear();
            foreach (var certificat in _certificatRepository.GetAll())
            {
                Certificats.Add(certificat);
            }
            Message = string.Empty;
        }
        catch (Exception ex)
        {
            MessageEstErreur = true;
            Message = "Chargement impossible : " + ex.Message;
        }
    }

    private void Rechercher()
    {
        try
        {
            Certificats.Clear();
            var source = string.IsNullOrWhiteSpace(Recherche)
                ? _certificatRepository.GetAll()
                : _certificatRepository.Search(Recherche.Trim());

            foreach (var certificat in source)
            {
                Certificats.Add(certificat);
            }
        }
        catch (Exception ex)
        {
            MessageEstErreur = true;
            Message = "Recherche impossible : " + ex.Message;
        }
    }

    private void Revoquer()
    {
        if (CertificatSelectionne == null) return;

        bool succes = _certificatService.Revoquer(CertificatSelectionne.IdCertificat, out var message);
        MessageEstErreur = !succes;
        Message = message;
        if (succes) Charger();
    }

    private void Regenerer()
    {
        if (CertificatSelectionne == null) return;

        bool succes = _certificatService.Regenerer(CertificatSelectionne.IdCertificat, out var message);
        MessageEstErreur = !succes;
        Message = message;
        if (succes) Charger();
    }
}
