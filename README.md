<p align="center">
</p>


<h1 align="center">Asdoria Import Bundle</h1>

<p align="center">Simply import resource intro sylius by messenger component</p>

## Features


+ import resource intro sylius by messenger componant
 
 

## Installation

---
1. run `composer require asdoria/sylius-import-plugin`

```PHP
Asdoria\SyliusImportPlugin\AsdoriaSyliusImportPlugin::class => ['all' => true],
```

2. Import config in `config/packages/_sylius.yaml`
```yaml
imports:
    - { resource: "@AsdoriaSyliusImportPlugin/Resources/config/config.yaml"}
```


2. Import config in `config/packages/_sylius.yaml`
```yaml
imports:
    - { resource: "@AsdoriaSyliusImportPlugin/Resources/config/config.yaml"}
```
3. Import messenger transport
```php
php bin/console messenger:setup-transports
```
4. Create your symfony command for push message into the queues

```php
<?php

namespace App\Command;


use App\Entity\User\ShopUser;
use Asdoria\SyliusImportPlugin\Configurator\Configuration;
use Asdoria\SyliusImportPlugin\Configurator\ConfigurationInterface;
use Asdoria\SyliusImportPlugin\Message\ImportNotification;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Entity\Customer\Customer;
/**
 * Class ImportCustomerCommand
 * @package App\Command
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class ImportCustomerCommand extends Command
{
    const _SQL = "select 
JSON_OBJECT('email', email, 'lastName',lastname, 'firstName', firstname, 'companyName', company, 'professional', is_company, 'siret', siret, 'gender', case 
     WHEN id_gender = 1 THEN 'm'
	 WHEN id_gender = 2 THEN  'f'
else  'u' end, 'createdAt', date_add, 'updatedAt' , date_upd,'acceptPolicy',optin, 'subscribedToNewsletter', newsletter, 'birthday', birthday ) as customer,
1 as enabled,
passwd as password,
secure_key as salt,
email as username
from bdi_customer where id_shop = 1 and active = 1 limit 200 ;";
    protected Connection $connection;
    protected MessageBusInterface $bus;
    /**
     *
     */
    public function __construct(
        MessageBusInterface $bus,
        Connection $connection
    ) {
        parent::__construct();
        $this->connection = $connection;
        $this->bus = $bus;
    }
    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('app:import:customer')
            ->setDescription(
                'Import Prestashop Customer.'
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->connection->getConfiguration()->setSQLLogger(null);
            $reader = $this->connection->fetchAllAssociative(self::_SQL);
            $config = $this->getConfiguration();
            foreach ($reader as $row) {
                $this->bus->dispatch(new ImportNotification($row, ShopUser::class, $config));
            }
        } catch (\Throwable $e) {
            $output->writeln('<error>Error during import customer , aborting with rollback : done error</error>');
            $output->writeln($e->getMessage());
        }

        return 0;
    }

    protected function getConfiguration(): ConfigurationInterface {
        $configuration = new Configuration();
        $configuration->setIdentifier('username');
        $configuration->setProvider(ConfigurationInterface::_PRESTASHOP_PROVIDER);
        $configuration->setUpdater(true);

        return $configuration;
    }
}
```

5. Play the messanger consume worker for unstack the queues

```php
php bin/console messenger:consume asdoria_import -vv
```


