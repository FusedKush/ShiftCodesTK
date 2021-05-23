# ShiftCodesTK PHP Modules #

The PHP `/modules` directory contains the procedural definition files for custom *Modules*.
These definition files define *Namespaces*, *Constant Values*, and procedural *Functions*. 

Modules may also define *Classes*, which can be found in the [`/classes`](../classes/README.md) directory.

Modules must **not** perform any *State-Changing* operations, and **must** be allowed to be loaded in any order without causing conflicts with other modules.