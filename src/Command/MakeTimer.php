<?php

namespace Ledc\Timer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webman\Console\Util;

/**
 * 命令：创建Timer
 */
class MakeTimer extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'make:timer';

    /**
     * @var string
     */
    protected static $defaultDescription = 'Make Timer';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, '定时器类名');
        $this->addArgument('interval', InputArgument::OPTIONAL, '定时间隔(秒)', '60');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = trim($input->getArgument('name'));
        $interval = $input->getArgument('interval');
        $class = Util::nameToClass($name);
        $file = app_path() . "/timer/{$class}Timer.php";
        if (is_file($file)) {
            $output->writeln("存在文件：" . $file);
        } else {
            $this->createTimer($file, $class, $interval);
        }

        return self::SUCCESS;
    }

    /**
     * @param string $file
     * @param string $class
     * @param int $interval
     * @return void
     */
    protected function createTimer(string $file, string $class, int $interval): void
    {
        $path = pathinfo($file, PATHINFO_DIRNAME);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $content = <<<EOF
<?php

namespace app\\timer;

use Ledc\\Timer\\TimerInterface;

/**
 * 通知
 */
class {$class}Timer implements TimerInterface
{
    /**
     * @return int
     */
    public function interval(): int
    {
        return $interval;
    }

    /**
     * @param bool|int \$timer_id
     * @return void
     */
    public function invoke(bool|int \$timer_id): void
    {
        //todo... 业务逻辑
    }
}

EOF;

        file_put_contents($file, $content);
    }
}