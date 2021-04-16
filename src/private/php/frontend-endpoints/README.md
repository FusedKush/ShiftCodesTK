# Frontend Endpoints #
These are the frontend *endpoints* that expose backend functionality to the public.

See the `README.md` in each directory for information on each of the *Endpoints*.

## Table of Contents ##
### [`/get/`](get/README.md) ### 
Endpoints used to *Read* data from the server.
  
| Endpoint                                  | Description                                                                               |
| ---                                       | ---                                                                                       |
| [`/get/token`](get/README.md#gettoken)    | Retrieve the *Request Token* belonging to the Current Session.                            |
| [`/get/changelogs`]()                     | **Currently Unavailable** ~~Retrieve a number of *Update Changelogs* from the database.~~ |

#### [`/get/account/`]() #### 
Endpoints used to query information related to *User Accounts*.

| Endpoint                                          | Description                                                           |
| ---                                               | ---                                                                   |
| [`/get/account/check_username_availability`]()    | Check if a given *Username* is currently available or unavailable.    |
| [`/get/account/profile-card-data`]()              | Fetch the necessary data to generate a *Profile Card* for a User.     |
| [`/get/account/profile_picture`]()                | Fetch the *Profile Picture* of a given user.                          |

#### [`/get/shift/`]() #### 
Endpoints used to query *SHiFT Codes & Data*.

| Endpoint                  | Description                                                       |
| ---                       | ---                                                               |
| [`/get/shift/codes`]()    | Fetch *SHiFT Codes* from the repository.                          |
| [`/get/shift/stats`]()    | Retrieve statistics related to *New* & *Expiring SHiFT Codes*.    |
| [`/get/shift/updates`]()  | Check for updates to the SHiFT Code Repository.                   |

### [`/post/`]() ###
Endpoints used to *Write* data to the server.

#### [`/post/account/`]() #### 
Endpoints used to update data related to *User Accounts*.

| Endpoint                                 | Description                                                            |
| ---                                      | ---                                                                    |
| [`/post/account/change-password`]()      | Change the *User's Password* to a new value.                           |
| [`/post/account/change-username`]()      | Change the *Username* of the User to a new value.                      |
| [`/post/account/danger-zone`]()          | **Currently Unavailable** ~~*Disable or Delete* the User's Account.~~  |
| [`/post/account/update-profile`]()       | Update the *Profile Information* of the User's Account.                |
| [`/post/account/update-stats-privacy`]() | Update the *Profile Stats Privacy Preference* of the User.             |

#### [`/post/auth/`]() #### 
Endpoints used to authenticate the User.

| Endpoint                  | Description                               |
| ---                       | ---                                       |
| [`/post/auth/login`]()    | Log the User in to their User Account.    |
| [`/post/auth/logout`]()   | Log the User out of their User Account.   |

#### [`/post/shift/`]() #### 
Endpoints used to write data related to *SHiFT Codes*.

| Endpoint                      | Description                                                       |
| ---                           | ---                                                               |
| [`/post/shift/delete`]()      | Delete a given *SHiFT Code* from the repository.                  |
| [`/post/shift/redeem`]()      | *Redeem* or *Unredeem* a SHiFT Code for the given user.           |
| [`/post/shift/shift-code`]()  | Add or Update a *SHiFT Code*.                                     |