# ForecastApp.com API PHP
Simple API client for Harvest's forecastapp.com

    $forecastAPI = new \pro\data5\ForecastAppAPI('testmail@gmail.com', 'mylongpass', '123479');
    $usersList = $forecastAPI->people();
    var_dump($usersList);

    
![App ID is in your URL](where_is_id.png)

App ID is in your URL

### Methods 

- getPeople() - all peoples list
- getMyUserID() - your account ID
- getMyUserInfo() - your or any other account info
- getUserInfo($userId) - any account info
- getAllAssignments($startDate, $endDate) - any all assignments by date (ex.: $endDate=2016-10-30, $startDate=2016-09-26)
- getAssignmentsByUser($userID, $startDate, $endDate) - any user assignments by date
- getProjects() - all projects
- getProject($projectID) - single project
- getAllMilestones($startDate, $endDate) - milestones for all projects
- getProjectMilestones($projectID, $startDate, $endDate) - milestones for single projects

Please star if it was helpful. Fill free to add push requests. You can hire author [on Upwork](https://www.upwork.com/freelancers/~0110e79b44736be7ab).

(c) [Solokhin Ilya](http://data5.pro)