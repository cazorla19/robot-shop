<?php

namespace Instana\RobotShop\Ratings\EventListener;

use Psr\Log\LoggerInterface;

use OpenTelemetry\SDK\Trace\SpanExporter\ConsoleSpanExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;

echo 'Starting ConsoleSpanExporter' . PHP_EOL;

$tracerProvider =  new TracerProvider(
    new SimpleSpanProcessor(
        new ConsoleSpanExporter()
    )
);

$tracer = $tracerProvider->getTracer();

//start a root span
$rootSpan = $tracer->spanBuilder('root')->startSpan();
//future spans will be parented to the currently active span
$rootScope = $rootSpan->activate();

class InstanaDataCenterListener
{
    private static $dataCenters = [
        "asia-northeast2",
        "asia-south1",
        "europe-west3",
        "us-east1",
        "us-west1"
    ];

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke()
    {
        try {
            $span1 = $tracer->spanBuilder('invoke')->startSpan();
            $span1Scope = $span1->activate();
            try {
                $span2 = $tracer->spanBuilder('annotate')->startSpan();
                $dataCenter = self::$dataCenters[array_rand(self::$dataCenters)];
                $entry->annotate('datacenter', $dataCenter);
                $span2->end();
            } finally {
                $span1Scope->detach();
                $span1->end();
            }
        } catch (Exception $exception) {
            $this->logger->error('Unable to annotate entry span: %s', $exception->getMessage());
        } finally {
            //ensure span ends and scope is detached
            $rootScope->detach();
            $rootSpan->end();
        }
    }
}
