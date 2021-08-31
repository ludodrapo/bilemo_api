<?php

namespace App\DataFixtures;

use App\Entity\Item;
use App\Entity\User;
use App\Entity\Client;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class AppFixtures
 * @package App\DataFixtures
 */
class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $hasher;

    /**
     * AppFixtures constructor.
     * @param UserPasswordHasherInterface $hasher
     */
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $userIndex = 1;

        for ($i = 1; $i <= 10; $i++) {
            $client = new Client();
            $manager->persist(
                $client
                    ->setName('client' . $i)
                    ->setEmail('client' . $i . '@gmail.com')
                    ->setPassword($this->hasher->hashPassword($client, 'password'))
            );

            for ($j = 1; $j <= 50; $j++) {
                $user = new User();
                $manager->persist(
                    $user
                        ->setName('user' . $userIndex)
                        ->setEmail('user' . $userIndex . '@gmail.com')
                        ->setClient($client)
                );
                $userIndex++;
            }
        }

        $items = [
            ['Apple', 'Iphone SE'],
            ['Apple', 'Iphone XR'],
            ['Apple', 'Iphone 11'],
            ['Apple', 'Iphone 12'],
            ['Apple', 'Iphone 12 Pro'],
            ['Samsung', 'Galaxy Z Fold3 5G'],
            ['Samsung', 'Galaxy Z Flip3 5G'],
            ['Samsung', 'Galaxy S21 Ultra 5G'],
            ['Samsung', 'Galaxy S21+ 5G'],
            ['Samsung', 'Galaxy S21 5G'],
            ['Huawei', 'Mate 40 Pro'],
            ['Huawei', 'P40 Pro'],
            ['Huawei', 'P40 Pro+'],
            ['Huawei', 'P40'],
            ['Huawei', 'P40 lite'],
            ['Huawei', 'P40 lite 5G'],
            ['OPPO', 'Find X3 Pro'],
            ['OPPO', 'Find X3 Neo'],
            ['OPPO', 'Find X3 Lite'],
            ['OPPO', 'Find X2 Pro'],
            ['OPPO', 'Find X2 Neo'],
            ['OPPO', 'Reno4 Pro 5G'],
            ['OPPO', 'Reno4 5G'],
            ['OPPO', 'Reno4 Z 5G'],
            ['Xiaomi', 'Mi 11'],
            ['Xiaomi', 'Mi 11 Lite'],
            ['Xiaomi', 'Mi 11 5G'],
            ['Xiaomi', 'Mi 11 Lite 5G'],
            ['Xiaomi', 'Mi 11 Ultra'],
            ['Xiaomi', 'Redmi Note 8 2021'],
            ['Xiaomi', 'Redmi Note 10'],
            ['Xiaomi', 'Redmi Note 10 5G'],
            ['Xiaomi', 'Redmi Note 10S'],
            ['Xiaomi', 'Redmi Note 10 Pro'],
            ['Xiaomi', 'POCO F3'],
            ['Xiaomi', 'POCO M3'],
            ['Xiaomi', 'POCO X3 Pro']
        ];

        $prices = [199, 299, 399, 499, 599, 699, 799, 899, 999, 1099, 1199, 1299];
        $colors = ['White', 'Black', 'Red', 'Gold', 'Aqua Blue', 'Pink', 'Purple', 'Green'];
        $internalMemories = [16, 32, 64, 128, 256, 512];
        $screenSizes = ['5,3 inches', '5,7 inches', '6,4 inches', '6,5 inches'];

        foreach ($items as $phone) {
            $item = new Item();
            $manager->persist(
                $item
                    ->setBrand($phone[0])
                    ->setModel($phone[1])
                    ->setPrice($prices[array_rand($prices)])
                    ->setColor($colors[array_rand($colors)])
                    ->setInternalMemory($internalMemories[array_rand($internalMemories)])
                    ->setScreenSize($screenSizes[array_rand($screenSizes)])
                    ->setWaterResistant(true)
            );
        }

        $manager->flush();
    }
}
