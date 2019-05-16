# S.W.A.C.
Solution Web Application for Casting (all rights reserved)

This is a wbe application which will solve many of the problems that our Ballet Masters, Casting Directors and Dancers face daily.

Here are some of the important CAPABILITIES:
* create productions 
* create rehearsals and performances of those productions
* create castings for those rehearsals and performances 
* create locations
* create roles and role conflicts (see restriction below)
* view and print daily rehearsal schedule
* create notifications of emergency recasting 
* view and select through monthly calendar menu
* also update and delete most of those things
* make copying roles, role conflicts, and castings easy
* error handling


Here are some of the observed CONSTRAINTS:
* no dancer can be scheduled for a rehearsal over 3 hours at a time  (reherasal overbook)
* no dancer can rehearse over 6 hours in a day (daily limit)
* there are roles that cannot be cast by the same dancer in a performance, so that problem casting is impossible (role conflicts)
* when any casting changes due to artistic liberties or injury, a notification must be sent out
* casting changes must have color coded text as indicator
* only logged in users can see this schedule
* only priveleged users can see or use the admin menu
* secure passwords or active directory interaction


