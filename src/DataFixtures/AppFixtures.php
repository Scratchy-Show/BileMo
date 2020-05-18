<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Customer;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var Generator
     */
    protected $faker;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        $customers = [];
        $customerNameArray = ['Orange', 'BouyguesTelecom', 'SFR', 'Free'];


        // === 10 products ===
        $product = new Product();
        $product
            ->setBrand("Apple")
            ->setName("iPhone 11")
            ->setMemory("64 Go")
            ->setColor("Gris Sidéral")
            ->setPrice(629.99)
            ->setDescription("Décidément, l’appareil photo le plus populaire au monde ne cesse de se réinventer pour vous offrir de nouvelles perspectives.")
        ;
        $manager->persist($product);

        $product = new Product();
        $product
            ->setBrand("Apple")
            ->setName("iPhone Xs Max")
            ->setMemory("512 Go")
            ->setColor("Argent")
            ->setPrice(1559.99)
            ->setDescription("L’iPhone XS détient un écran Super Retina de 5,8 pouces et l’iPhone XS Max un écran de 6,5 pouces avec des panneaux OLED créés spécialement, pour un affichage HDR offrant des couleurs d’une qualité supérieure.")
        ;
        $manager->persist($product);

        $product = new Product();
        $product
            ->setBrand("Samsung")
            ->setName("Galaxy S20")
            ->setMemory("128 Go")
            ->setColor("Gris")
            ->setPrice(909.99)
            ->setDescription("Les smartphones qui changent la manière de prendre des photos et des vidéos")
        ;
        $manager->persist($product);

        $product = new Product();
        $product
            ->setBrand("Samsung")
            ->setName("Galaxy S20 Ultra")
            ->setMemory("128 Go")
            ->setColor("Noir")
            ->setPrice(1099.99)
            ->setDescription("Le fleuron coréen de ce début de décennie collectionne les cinq étoiles pour un sans-faute mérité. Il remplit sans complexe sa mission de vitrine technologique et se présente comme le meilleur des smartphones passés dans notre laboratoire.")
        ;
        $manager->persist($product);

        $product = new Product();
        $product
            ->setBrand("Huawei")
            ->setName("Mate 30 Pro")
            ->setMemory("256 Go")
            ->setColor("Noir")
            ->setPrice(729.99)
            ->setDescription("Le Huawei Mate 30 Pro est sans aucun doute l'un des meilleurs smartphones du marché. Techniquement, la promesse d'un mobile haut de gamme, endurant, puissant et excellent en photo est bien tenue.")
        ;
        $manager->persist($product);

        $product = new Product();
        $product
            ->setBrand("Huawei")
            ->setName("P40")
            ->setMemory("128 Go")
            ->setColor("Gris")
            ->setPrice(649.99)
            ->setDescription("Nouveau flagship de la marque Huawei, le P40 Pro est sans conteste un smartphone qui n’a pas fini de faire parler de lui. Oui, le téléphone est dépourvu des services Google, mais oui, c’est aussi le nouveau mètre étalon en matière de photographie.")
        ;
        $manager->persist($product);

        $product = new Product();
        $product
            ->setBrand("Asus")
            ->setName("ROG Phone 2")
            ->setMemory("512 Go")
            ->setColor("Noir")
            ->setPrice(899.99)
            ->setDescription("Avec cette nouvelle édition du ROG Phone, Asus montre tout son savoir-faire et sa maîtrise dans l'univers des produits gaming. En termes de puissance, ceux qui aiment jouer sur leur téléphone ne seront pas déçus, tout comme ceux qui cherchent seulement l'un des mobiles les plus efficaces du marché.")
        ;
        $manager->persist($product);

        $product = new Product();
        $product
            ->setBrand("Google")
            ->setName("Pixel 4 XL")
            ->setMemory("64 Go")
            ->setColor("Noir")
            ->setPrice(699.99)
            ->setDescription("Avec ce Pixel 4 XL, Google propose une expérience presque idéale. Le smartphone est tout simplement excellent que ce soit du côté de la photo, de l'écran ou des performances.")
        ;
        $manager->persist($product);

        $product = new Product();
        $product
            ->setBrand("Xiaomi")
            ->setName("Mi 10")
            ->setMemory("256 Go")
            ->setColor("Bleu")
            ->setPrice(759.99)
            ->setDescription("La recette du Xiaomi Mi 10 fonctionne très bien dans l'ensemble. Le fabricant chinois nous propose un mobile résolument haut de gamme dans son approche, qui n'a que peu de défauts.")
        ;
        $manager->persist($product);

        $product = new Product();
        $product
            ->setBrand("OnePlus")
            ->setName("7T Pro")
            ->setMemory("256 Go")
            ->setColor("Noir")
            ->setPrice(759.99)
            ->setDescription("Véritable alternative au OnePlus 7 sorti en mai dernier. Il s'agit d'un des plus puissants de cette fin d'année")
        ;
        $manager->persist($product);

        //  === 4 customers ===
        foreach ($customerNameArray as $customerName) {
            $customer = new Customer();
            $customer
                ->setUsername($customerName)
                ->setName($customerName)
                ->setEmail("test" . $customerName . "@exemple.fr")
                ->setRoles(['ROLE_ADMIN'])
                ->setPassword($this->encoder->encodePassword($customer, 'password'))
                ;

            $customers[] = $customer;

            $manager->persist($customer);
        }

        //  === 60 users ===
        for ($u = 0; $u < 60; $u++) {
            $user = new User();

            $user
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setAddress($faker->address)
                ->setZipcode($faker->postcode)
                ->setCity($faker->city)
                ->setCreatedAt($faker
                    ->dateTimeBetween($startDate = '-1 year', $endDate = 'now', $timezone = 'Europe/Paris'))
                ->setCustomer($faker->randomElement($customers))
            ;
            $manager->persist($user);
        }

        $manager->flush();
    }
}
