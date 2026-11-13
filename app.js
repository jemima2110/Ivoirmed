function afficherFormulaire() {
    const formulaire = document.getElementById("formulairePatient");
    if (formulaire) formulaire.classList.remove("hidden");
}
function masquerFormulaire() {
    const formulaire = document.getElementById("formulairePatient");
    if (formulaire) formulaire.classList.add("hidden");
}
function basculer(id) {
    const element = document.getElementById(id);
    if (element) element.classList.toggle("hidden");
}
