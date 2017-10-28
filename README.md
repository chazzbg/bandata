# BanData

A simple visualiser of the heart rate from the [MiBand Master](https://play.google.com/store/apps/details?id=blacknote.mibandmaster) App based on symfony4

### Рequirements

- php >7.0
- yarn
- composer
- sqlite3 

### How to run it 

1. `git clone https://github.com/chazzbg/bandata.git`
1. `composer install`
1. `yarn install`
1. `yanr run encore dev` 

### Internals

This app reads the database file from the app MiBand master

From MiBand master app, go to `Settings > Data > Data Export` and export `db.sqlite` file , then put it into `var/` folder. 
App reads the db from there and visualises Heart rate data

Additionally you can download the db file from GDrive
When exporting the database, export it into folder `MiBandMaster` in your main Gdrive folder

Register an OAuth Cliend ID for web application in GCP and put the secrets into env 
`GOOGLE_OAUTH_CLIENT_ID` and `GOOGLE_OAUTH_CLIENT_SECRET`

Then click `Auth` link into the top right corner to authorize the app, after that click `Sync` to sync the data
