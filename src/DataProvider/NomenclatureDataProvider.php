<?php

declare(strict_types=1);

namespace App\DataProvider;

class NomenclatureDataProvider
{
    public const PRICE_TYPE_NORMAL = 1;
    public const PRICE_TYPE_PROMOTIONAL = 2;
    public const PRICE_TYPE_SPECIAL = 3;

    public const MEDICAL_FORM_SPRAY = 100;
    public const MEDICAL_FORM_GEL = 110;
    public const MEDICAL_FORM_GLOSSA = 120;
    public const MEDICAL_FORM_GRANULES = 130;
    public const MEDICAL_FORM_DRAGETE = 140;
    public const MEDICAL_FORM_DROPS = 150;
    public const MEDICAL_FORM_CAPSULES = 160;
    public const MEDICAL_FORM_CARAMEL = 170;
    public const MEDICAL_FORM_CREAM = 180;
    public const MEDICAL_FORM_MED_PENCIL = 190;
    public const MEDICAL_FORM_OINTMENT = 200;
    public const MEDICAL_FORM_MIXTURE = 210;
    public const MEDICAL_FORM_TINCTURE = 220;
    public const MEDICAL_FORM_PASTE = 230;
    public const MEDICAL_FORM_POWDER = 240;
    public const MEDICAL_FORM_SOLUTION = 250;
    public const MEDICAL_FORM_SYRUP = 260;
    public const MEDICAL_FORM_SUPPOSITORIES = 270;
    public const MEDICAL_FORM_SUSPENSION = 280;
    public const MEDICAL_FORM_PILLS = 290;
    public const MEDICAL_FORM_EMULSION = 300;
    public const MEDICAL_FORM_OTHER = 310;

    /**
     * @return string[]
     */
    public static function medForms(): array
    {
        return [
            self::MEDICAL_FORM_SPRAY => 'Спрей',
            self::MEDICAL_FORM_GEL => 'Гель',
            self::MEDICAL_FORM_GLOSSA => 'Глоссет',
            self::MEDICAL_FORM_GRANULES => 'Гранулы',
            self::MEDICAL_FORM_DRAGETE => 'Драже',
            self::MEDICAL_FORM_DROPS => 'Капли',
            self::MEDICAL_FORM_CAPSULES => 'Капсулы',
            self::MEDICAL_FORM_CARAMEL => 'Карамель',
            self::MEDICAL_FORM_CREAM => 'Крем',
            self::MEDICAL_FORM_MED_PENCIL => 'Лек. карандаш',
            self::MEDICAL_FORM_OINTMENT => 'Мазь',
            self::MEDICAL_FORM_MIXTURE => 'Микстура',
            self::MEDICAL_FORM_TINCTURE => 'Настойка',
            self::MEDICAL_FORM_PASTE => 'Паста',
            self::MEDICAL_FORM_POWDER => 'Порошок',
            self::MEDICAL_FORM_SOLUTION => 'Раствор',
            self::MEDICAL_FORM_SYRUP => 'Сироп',
            self::MEDICAL_FORM_SUPPOSITORIES => 'Суппозитории',
            self::MEDICAL_FORM_SUSPENSION => 'Суспензия',
            self::MEDICAL_FORM_PILLS => 'Таблетки',
            self::MEDICAL_FORM_EMULSION => 'Эмульсия',
            self::MEDICAL_FORM_OTHER => 'Другое'
        ];
    }

    /**
     * @return string[]
     */
    public static function medFormsStringOnly(): array
    {
        return [
            'Спрей',
            'Гель',
            'Глоссет',
            'Гранулы',
            'Драже',
            'Капли',
            'Капсулы',
            'Карамель',
            'Крем',
            'Лек. карандаш',
            'Мазь',
            'Микстура',
            'Настойка',
            'Паста',
            'Порошок',
            'Раствор',
            'Сироп',
            'Суппозитории',
            'Суспензия',
            'Таблетки',
            'Эмульсия',
            'Другое'
        ];
    }

    /**
     * @param string $medicalForm
     * @return int|null
     */
    public static function getIntValueOfMedForms(string $medicalForm): ?int
    {
        return array_search($medicalForm, self::medForms()) ? array_search($medicalForm, self::medForms()) : null;
    }

    /**
     * @param int $key
     * @return string|null
     */
    public static function getStringValueOfMedForms(int $key): ?string
    {
        return array_key_exists($key, self::medForms()) ? self::medForms()[$key] : null;
    }

    /**
     * @param int|null $medicalForm
     * @return bool
     */
    public static function isMedicalFormAllowed(?int $medicalForm): bool
    {
        return in_array($medicalForm, self::medForms());
    }
}
