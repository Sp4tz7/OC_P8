# Contributing

Thank you for your interest to this project :blush:
Please follow the present contributing documentation if you want to participate to this project

## Issues
Report any issue concerning this project only in the [Issues](https://github.com/Sp4tz7/OC_P8/issues) channel. You can also report new functionalities or submit `pull requests`.
* Do not use this channel for your own code problems or difficulties. Please use [google](https://google.com) or [Stack Overflow](http://stackoverflow.com/).

## Bug report
Report only bugs concerning this repository

1. Do not report an existing bug, please use the [Issues search function before](https://github.com/Sp4tz7/OC_P8/issues).
2. Write your issue in english

## New features
If you have an enhancement suggestion, including completely new features or minor improvements to existing functionality.

This project is divided into several branches to maintain a complete visibility.

1. The `main` branch is the production environment 
2. The `develop` branch is a copy of `main` and contains many milestone branche
3. Create one subranch of develop for each new milestone. If the feature enter in one existing milestone, please use this branch.    
   Current milestones are: 
      - `User`
      - `Task`
4. Create a new branche in the milestone with the following template `[milestone]/issue/#[issue_id]/[bug/feature]`
   
   ```
   git checkout task
   git checkout -b task/issue/#<issue_number>/<feature-name>
   ```
   
## Pull request
Each `pull request` has to grow from the lower branch to the `main`

***issue*** => ***milestone*** => ***develop*** => ***main***

Create a new pull request with a precise description and the purpose of the request. Add the issue number in the description.

