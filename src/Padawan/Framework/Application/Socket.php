<?php

namespace Padawan\Framework\Application;

use Amp;
use Padawan\Command\AsyncCommand;
use Padawan\Command\KillCommand;
use Padawan\Command\ListCommand;
use Padawan\Framework\Application;
use Padawan\Command\CompleteCommand;
use Padawan\Command\UpdateCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Padawan\Framework\Application\Socket\SocketOutput;

/**
 * Class Socket
 */
class Socket extends Application
{
    public function __construct()
    {
        parent::__construct("Padawan Server");
    }

    public function handle($request, SocketOutput $output)
    {
        if (!$request
            || !property_exists($request, "command")
            || !property_exists($request, "params")) {
            yield $output->write(json_encode([
                "error" => "Bad request"
            ]));
            return;
        }
        $arrayForInput = ['command' => $request->command];
        foreach($request->params as $key=>$value) {
            $arrayForInput[$key] =  $value;
        }
        $input = new ArrayInput($arrayForInput);
        $command = $this->find($request->command);
        try {
            if ($command instanceof AsyncCommand) {
                yield Amp\resolve($command->run($input, $output));
            }
        } catch (\Exception $e) {
            printf("Error: %s\n", $e->getMessage());
            yield $output->write(json_encode([
                "error" => $e->getMessage()
            ]));
        }
    }

    protected function loadCommands()
    {
        $this->add(new CompleteCommand);
        $this->add(new UpdateCommand);
        $this->add(new ListCommand);
        $this->add(new KillCommand);
    }
}
