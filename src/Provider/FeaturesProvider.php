<?php
/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 10/01/2019
 * Time: 10:16
 */

namespace App\Provider;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class FeaturesProvider extends AbstractController
{
    public function getFeatures()
    {
        try {
            return Yaml::parseFile(__DIR__ . '/features.yaml')['data'];
        }
        catch (ParseException $exception) {
            printf('Unable to parse the YAML file: %s', $exception->getMessage());
        }
    }
}