<?php namespace DependChecker;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class DependCommand extends Command {

    protected $contact;

    /** @var array  */
    protected $violations = array();

    public function __construct() {
        parent::__construct();
    }

    protected function configure() {
        $this
            ->setName('check')
            ->setDescription('Check if something depends on sth different.')
            ->addArgument(
                'dependencyFile',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'toCheckFile',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'outputFile',
                InputArgument::REQUIRED,
                ''
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        if(!$dependencies = file_get_contents($input->getArgument('dependencyFile'))) {
            throw new \Exception('Dependency File not found.');
        }
        if(!$toCheck = file_get_contents($input->getArgument('toCheckFile'))) {
                throw new \Exception('To check File not found.');
        }
        $outputFile = $input->getArgument('outputFile');
        $dependencies = json_decode($dependencies, true);
        $toCheck = json_decode($toCheck, true);
        var_dump($toCheck);
        $results = $this->buildResultArray($toCheck, $dependencies);
        $resultsString = implode("\n", $results);

        if(file_put_contents($outputFile, $resultsString) !== false){
           $output->writeln("Outputfile written to: " . $outputFile);
        } else {
            throw new \Exception("Could not write into output file: " . $outputFile);
        }
    }

    protected function check($dependencies, $check) {
        $dependency = $dependencies[$check['subject']];
        if(!is_array($dependency))
            return false;
        return in_array($check['argument'], $dependency);
    }

    protected function addViolation($message) {
        $this->violations[] = $message;
    }

    /**
     * @param $toCheck array
     * @param $dependencies array
     * @return array
     */
    protected function buildResultArray($toCheck, $dependencies)
    {
        $results = array();
        foreach ($toCheck as $check) {
            if ($this->check($dependencies, $check)) {
                $results[] = $check['msgPass'];
            } else {
                $results[] = $check['msgFail'];
            }
        }
        return $results;
    }

}