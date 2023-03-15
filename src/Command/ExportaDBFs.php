<?php

namespace App\Command;

use App\Service\ExportadoService;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Comando Symfony que realizar la exportación de novedades de matrículas y contraseñas del Sistema a archivos DBFs.
 * @author jialarcon <jialarcon@justiciasantafe.gov.ar>
 */
#[AsCommand(
    name: 'app:exporta-dbfs',
    description: 'Realiza la exportación de novedades de colegios a archivos .DBF',
    hidden: false
)]
class ExportaDBFs extends Command
{

    private $_logger;
    private $_exportadoService;

    /**
     * Constructor
     */
    public function __construct(LoggerInterface $logger, ExportadoService $exportadoService)
    {
        $this->_logger = $logger;
        $this->_exportadoService = $exportadoService;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setHelp('Realiza la exportación de novedades de colegios a archivos .DBF');
    }

    /**
     * Ejecución del comando
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $output->writeln('Inicio exportación de novedades de matrículas y claves');
            $this->_logger->info('Inicio exportación de novedades de matrículas y claves');
            $retorno = $this->_exportadoService->exportarNovedades();
            $output->writeln($retorno ? 'Sin novedades para exportar' : 'Exportación de novedades de matrículas y claves realizado con exito!!');
            $this->_logger->info($retorno ? 'Sin novedades para exportar' : 'Exportación de novedades de matrículas y claves realizado con exito!!');
        } catch (Exception $th) {
            $this->_logger->error('Error en Exportación de novedades de matrículas y claves');
            $this->_logger->error($th->getMessage());
            $this->_logger->error($th->getTraceAsString());
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }
}
