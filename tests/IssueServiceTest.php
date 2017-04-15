<?php

use App\Issue as Issue;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class IssueServiceTest extends TestCase
{
    
    use DatabaseMigrations;
    
    public function testDB()
    {
        \Log::info('app env: ' . env('APP_ENV'));
        \Log::info('db connection: ' . env('DB_CONNECTION'));
        \Log::info('db defualt: ' . config('database.default'));
        if (App::environment('local')) {
            \Log::info('env is local');
        } else if (App::environment('testing')) {
            \Log::info('env is testing');
        } else {
            \Log::info('env is unknown');
        }
        
        $actual1 = $this->createAnIssue(1);
        $actual2 = $this->createAnIssue(2);

        $result = Issue::where('title', $actual1->title)->first();

        $this->assertEquals($result->title, $actual1->title);
        $this->assertNotEquals($result->title, $actual2->title);
    }

    public function testReorderFourToTwo() {
        $this->runReorderTest(5, 4, 2);
    }
    public function testReorderFiveToOne() {
        $this->runReorderTest(5, 5, 1);
    }
    public function testReorderThreeToOne() {
        $this->runReorderTest(5, 3, 1);
    }
    public function testReorderOneToFive() {
        $this->runReorderTest(5, 1, 5);
    }
    public function testReorderTwoToFour() {
        $this->runReorderTest(5, 2, 4);
    }
    public function testReorderThreeToFive() {
        $this->runReorderTest(5, 3, 5);
    }
    public function testReorderThreeToTwo() {
        $this->runReorderTest(5, 3, 2);
    }
    public function testReorderTwoToThree() {
        $this->runReorderTest(5, 2, 3);
    }
    
    public function testLastPriorityOrder()
    {
        Issue::truncate();
        $this->createIssues(5);
        
        $issueService = new App\Services\IssueService(new Issue());
        $result = $issueService->getLastPriorityOrder();
        
        $this->assertEquals(5, $result);
        
    }
    
    public function testLastPriorityOrderEmptyTable()
    {
        Issue::truncate();
        
        $issueService = new App\Services\IssueService(new Issue());
        $result = $issueService->getLastPriorityOrder();
        
        $this->assertNull($result);
        
    }    

    private function runReorderTest($totalCases, $toReorder, $newPlace)
    {
        Issue::truncate();
        $testEmpty = Issue::all();
        $this->assertEquals(0, $testEmpty->count());
        
        $issuesToCreate = $this->createIssues($totalCases);
        
        $issueService = new App\Services\IssueService(new Issue());
        $result = $issueService->reorder($issuesToCreate[$toReorder-1], $issuesToCreate[$newPlace-1]);
        
        $this->assertTrue($result);

        $issues = DB::table('issues')->orderBy('priority_order')->get();
        
        foreach ($issues as $index => $issue) {
            $this->assertEquals($index+1,$issue->priority_order, $issue->title . ' with wrong prio: ' . $issue->priority_order);
        }
    }
    
    private function createAnIssue($num = 1)
    {
        $issue = Issue::create(
                [
                    'project_id' => 1, 
                    'title' => 'Test ' . $num, 
                    'priority_order' => $num,
                    'user_id' => 1,
                    'type_id' => 1,
                    'sprint_id' => 1,
                    'status_id' => 1
                ]);
        return $issue;
    }
    
    private function createIssues($numToCreate = 2)
    {
        $issues = [];
        \Log::info('create ' . $numToCreate . ' test issues');
        for($num = 1; $num <= $numToCreate; $num++) {
            $issues[] = $this->createAnIssue($num);
        }
        \Log::info(count($issues) . ' test issues created.');
        return $issues;
    }

}
