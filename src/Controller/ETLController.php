<?php
namespace IASETL\Controller;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
class ETLController
{
    private Connection $connection;
    public function __construct(Connection $etlConnection)
    {
        $this->connection = $etlConnection;
    }
    #[Route('/etl/test', name: 'ias_etl_test')]
    public function test(): JsonResponse
    {
        return new JsonResponse(['status' => 'ETL bundle works!']);
    }
    #[Route('/etl/test-db', name: 'ias_etl_test_db')]
    public function testDb(): JsonResponse
    {
        $result = $this->connection->fetchAssociative('SELECT NOW()');
        return new JsonResponse($result);
    }
}
