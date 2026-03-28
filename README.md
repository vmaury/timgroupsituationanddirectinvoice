# TIMGROUPSITUATIONANDDIRECTINVOICE FOR [DOLIBARR ERP & CRM](https://www.dolibarr.org)

## Fonctionnalités

Ce module permet d'utiliser 2 templates de facture client intégrant la possibilité d'ajouter des paiements directs aux fournisseurs :
  - "facture_timgroup_situation" pour les factures client de situation
  - "facture_timgroup_crabe" pour les factures client standards

Lorsque vous êtes sur une facture client, sous la liste des produits, vous pouvez sélectionner une ou plusieurs factures fournisseurs à intégrer dans votre facture pdf comme étant des paiements directs aux fournisseurs. La liste des factures fournisseurs possibles est l'ensemble des factures fournisseurs du projet de statut "Payée" ou "Impayée".
Si une facture fournisseur a déjà été décomptée dans une facture client, elle ne pourra plus être sélectionnée comme paiement direct dans une autre facture client.

Les montants de celles-ci seront retranchés du montant total TTC de la facture pour le calcul du total net, en plus des retenues de garantie s'il y en a.


####Attention !
La sélection des factures fournisseurs ne tient pas compte du statut de celles-ci ("Payée" ou "Impayée") : ce statut est rappelé dans le tableau des paiements directs mais c'est à l'utilisateur d'effectuer manuellement le paiement d'une facture si elle est impayée.

Cela peut se faire en cliquant directement sur la référence de la facture fournisseur ce qui ouvre la facture dans un autre onglet.
L'utilisateur peut ainsi créer un paiement pour celle-ci.

## Licenses

### Main code

GPLv3 or (at your option) any later version. See file COPYING for more information.

### Documentation

All texts and readme's are licensed under [GFDL](https://www.gnu.org/licenses/fdl-1.3.en.html).
