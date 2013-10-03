<?php

namespace Liip\TranslationBundle\Persistence\Propel\Formatter;

use Liip\TranslationBundle\Model\Unit as ModelUnit;
use Liip\TranslationBundle\Model\Translation as ModelTranslation;

/**
 * Propel formatter for Liip\TranslationBundle\Model\Unit object
 *
 * This file is part of the LiipTranslationBundle. For more information concerning
 * the bundle, see the README.md file at the project root.
 *
 * @package Liip\TranslationBundle\Persistence
 * @version 0.0.1
 *
 * @license http://opensource.org/licenses/MIT MIT License
 * @author David Jeanmonod <david.jeanmonod@liip.ch>
 * @author Gilles Meier <gilles.meier@liip.ch>
 * @copyright Copyright (c) 2013, Liip, http://www.liip.ch
 */
class UnitFormatter extends \PropelFormatter {

    /**
     * {@inheritdoc}
     */
    public function format(\PDOStatement $stmt)
    {
        $allUnits = array();
        $unitTree = array();
        foreach($stmt as $translation) {
            $domain = $translation['DOMAIN'];
            $key = $translation['KEY'];
            if (!isset($unitTree[$domain][$key])) {
                $unit = new ModelUnit($domain, $key, $this->unserialized($translation['METADATA']));
                $allUnits[] = $unit;
                $unitTree[$domain][$key] = $unit;
            }
            $unitTree[$domain][$key]->addTranslation(
                new ModelTranslation($translation['VALUE'], $translation['LOCALE'], $unitTree[$domain][$key], $this->unserialized($translation[7]))
            );
        }

        return $allUnits;
    }

    /**
     * {@inheritdoc}
     */
    public function formatOne(PDOStatement $stmt)
    {
        $units = $this->format($stmt);
        return reset($units);
    }

    /**
     * {@inheritdoc}
     */
    public function isObjectFormatter()
    {
        return false;
    }

    /**
     * Unserialized ARRAY data
     * (Copy/paste from \PHP5ObjectBuilder::addArrayAccessorBody())
     *
     * @param $data string
     *
     * @return array
     */
    protected function unserialized($data)
    {
        return $data ? explode(' | ', $data) : array();
    }

}