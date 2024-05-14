<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Entity;

/**
 * Description of MyTest
 *
 * @author Boubacar Traoré
 */
class MyTest {

    private static $prenoms = ['Boubacar', 'Amadou', 'Moussa', 'Adama', 'Ousmane', 'Alou', 'Fanta', 'Aminata', 'Oumou', 'Mariam'];
    private static $noms = ['Traoré', 'Diarra', 'Dembélé', 'Koné', 'Diallo', 'Sidibé', 'Kouyaté', 'Sangaré', 'Touré', 'Diabaté'];
    private static $lieux = ['Bamako', 'Kayes', 'Koulikoro', 'Sikasso', 'Ségou', 'Mopti', 'Tombouctou', 'Gao', 'Kidal'];
    private static $quartiers = ['Hamdallaye', 'Lafiabougou', 'Faladiè', 'Badialan', 'Badalabougou', 'Kalaban'];
    private static $sexes = ['M', 'F'];
    private static $dates = ['01-01-1980', '05-04-1982', '20-09-1984', '12-03-1986', '15-08-1990', '08-12-1997', 01-01-1980];
    
    public static function generateEtudiant(Etudiant $etudiant) {
        $etudiant->setPrenom(self::$prenoms[rand(0, count(self::$prenoms)-1)]);
        $etudiant->setNom(self::$noms[rand(0, count(self::$noms)-1)]);
        $etudiant->setDateNaiss(new \DateTime(self::$dates[rand(0, count(self::$dates)-1)]));
        $etudiant->setLieuNaiss(self::$lieux[rand(0, count(self::$lieux)-1)]);
        $etudiant->setNom(self::$noms[rand(0, count(self::$noms)-1)]);
        $etudiant->setQuartier(self::$quartiers[rand(0, count(self::$quartiers)-1)]);
        $etudiant->setSexe(self::$sexes[rand(0, count(self::$sexes)-1)]);
        
        $phone = rand(5, 9);
        for($i=1;$i<=6;$i++){
            $phone .= rand(0, 9);
        }
        $etudiant->setTelephone($phone);
        
        $annee = '20';
        for($i=1;$i<=2;$i++){
            $annee .= rand(0, 9);
        }

        $etudiant->setAnneeBac($annee);
        $etudiant->setAnneeDef($annee-3);
        
        return $etudiant;
    }

}
