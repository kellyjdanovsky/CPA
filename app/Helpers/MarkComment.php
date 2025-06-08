<?php

namespace App\Helpers;

class MarkComment
{
    /**
     * Retourne une remarque pédagogique en fonction de la note sur 20
     * 
     * @param float $note Note sur 20
     * @return string Remarque pédagogique
     */
    public static function getComment($note)
    {
        // Convertir la note en valeur sur 20 si nécessaire
        $note_sur_20 = $note;
        if ($note > 20) {
            $note_sur_20 = $note / 5; // Si la note est sur 100, diviser par 5 pour obtenir sur 20
        }
        
        // Déterminer la remarque en fonction de la tranche de notes
        if ($note_sur_20 >= 0 && $note_sur_20 < 5) {
            return "Beaucoup d'erreurs. Tu peux rebondir, ne perds pas courage.";
        } elseif ($note_sur_20 >= 5 && $note_sur_20 < 8) {
            return "Bases fragiles. Garde confiance, tu peux progresser avec régularité.";
        } elseif ($note_sur_20 >= 8 && $note_sur_20 < 10) {
            return "Des idées présentes. Tu es sur la bonne voie, continue à t'accrocher.";
        } elseif ($note_sur_20 >= 10 && $note_sur_20 < 12) {
            return "Juste le minimum. Tu peux aller plus loin avec un peu plus d'effort.";
        } elseif ($note_sur_20 >= 12 && $note_sur_20 < 14) {
            return "Travail correct. Continue ainsi, tu avances bien.";
        } elseif ($note_sur_20 >= 14 && $note_sur_20 < 16) {
            return "Bon niveau. Poursuis tes efforts pour viser plus haut.";
        } elseif ($note_sur_20 >= 16 && $note_sur_20 < 18) {
            return "Très bon travail. Tu es sur une excellente dynamique, bravo !";
        } elseif ($note_sur_20 >= 18 && $note_sur_20 <= 20) {
            return "Résultat exceptionnel. Continue à viser l'excellence, félicitations !";
        } else {
            return ""; // Pour les notes hors limites ou non définies
        }
    }
    
    /**
     * Retourne un commentaire général de l'enseignant en fonction de la moyenne générale sur 20
     * 
     * @param float $moyenne Moyenne générale sur 20
     * @return string Commentaire de l'enseignant
     */
    public static function getGeneralComment($moyenne)
    {
        // Convertir la moyenne en valeur sur 20 si nécessaire
        $moyenne_sur_20 = $moyenne;
        if ($moyenne > 20) {
            $moyenne_sur_20 = $moyenne / 5; // Si la moyenne est sur 100, diviser par 5 pour obtenir sur 20
        }
        
        // Déterminer le commentaire en fonction de la tranche de moyennes
        if ($moyenne_sur_20 >= 0 && $moyenne_sur_20 < 5) {
            return "Moyenne très faible. Il faut persévérer, chaque progrès compte.";
        } elseif ($moyenne_sur_20 >= 5 && $moyenne_sur_20 < 8) {
            return "Résultats insuffisants, mais des efforts peuvent relancer la dynamique.";
        } elseif ($moyenne_sur_20 >= 8 && $moyenne_sur_20 < 10) {
            return "Moyenne fragile. Du potentiel à développer avec plus de régularité.";
        } elseif ($moyenne_sur_20 >= 10 && $moyenne_sur_20 < 12) {
            return "Moyenne juste. Des bases sont posées, il faut les renforcer.";
        } elseif ($moyenne_sur_20 >= 12 && $moyenne_sur_20 < 14) {
            return "Moyenne correcte. Il faut continuer à s'investir pour consolider les acquis.";
        } elseif ($moyenne_sur_20 >= 14 && $moyenne_sur_20 < 16) {
            return "Bon travail. Un bel investissement à maintenir.";
        } elseif ($moyenne_sur_20 >= 16 && $moyenne_sur_20 < 18) {
            return "Très bon niveau. L'élève est sérieux et régulier.";
        } elseif ($moyenne_sur_20 >= 18 && $moyenne_sur_20 <= 20) {
            return "Excellent parcours. Un exemple à suivre, bravo !";
        } else {
            return ""; // Pour les moyennes hors limites ou non définies
        }
    }
    
    /**
     * Retourne une couleur CSS en fonction de la note sur 20
     * 
     * @param float $note Note sur 20
     * @return string Code couleur CSS
     */
    public static function getCommentColor($note)
    {
        // Convertir la note en valeur sur 20 si nécessaire
        $note_sur_20 = $note;
        if ($note > 20) {
            $note_sur_20 = $note / 5; // Si la note est sur 100, diviser par 5 pour obtenir sur 20
        }
        
        // Déterminer la couleur en fonction de la tranche de notes
        if ($note_sur_20 >= 0 && $note_sur_20 < 5) {
            return "text-danger"; // Rouge pour les notes très basses
        } elseif ($note_sur_20 >= 5 && $note_sur_20 < 10) {
            return "text-warning"; // Orange pour les notes insuffisantes
        } elseif ($note_sur_20 >= 10 && $note_sur_20 < 14) {
            return "text-primary"; // Bleu pour les notes moyennes
        } elseif ($note_sur_20 >= 14 && $note_sur_20 < 18) {
            return "text-success"; // Vert pour les bonnes notes
        } elseif ($note_sur_20 >= 18 && $note_sur_20 <= 20) {
            return "text-purple"; // Violet pour les notes excellentes
        } else {
            return "text-muted"; // Gris pour les notes hors limites ou non définies
        }
    }
}