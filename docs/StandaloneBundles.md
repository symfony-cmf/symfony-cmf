Some bundles are exposed as standalone bundles because somebody might want to
use them without using the Symfony CMF.

To update the subtree repository, according to
http://help.github.com/split-a-subpath-into-a-new-repo/

    git clone git@github.com:symfony-cmf/symfony-cmf.git DoctrinePHPCRBundle
    cd DoctrinePHPCRBundle
    git filter-branch --prune-empty --subdirectory-filter src/Symfony/Bundle/DoctrinePHPCRBundle/ master
    git remote add bundle git@github.com:symfony-cmf/DoctrinePHPCRBundle.git
    git remote update
    git push bundle master

NOTE: You have to do a fresh checkout of symfony-cmf each time you want to
update the subtree repository. This seems not to hurt anything however.
If somebody finds a way to keep the repo and not having to do a fresh checkout
for each time, please tell and document it here...


## History

Some bundles where already existing as standalone repositories. Those where
merged into the symfony-cmf repository according to the guide at
http://help.github.com/subtree-merge/ which seems to be no more than just copy
the files. All commit history is lost and we just kept Archive_ repositories
of the PhpcrCommandsBundle and DoctrinePHPCRBundle for reference.

https://github.com/symfony-cmf/Archived_PhpcrCommandsBundle

**Pulling in the changes later does not work, it completely replaces the
repository with the subtree.**

Here is a record of the commands used to import the repositories:

    git remote add doctrinephpcrbundle git://github.com/symfony-cmf/DoctrinePHPCRBundle.git
    git fetch doctrinephpcrbundle
    git checkout -b doctrinephpcrbundle_branch doctrinephpcrbundle/master
    git checkout master
    git read-tree --prefix=src/Symfony/Bundle/DoctrinePHPCRBundle -u doctrinephpcrbundle_branch
    git commit -m "imported DoctrinePHPCRBundle"

    git remote add phpcrcommandsbundle git://github.com/symfony-cmf/PhpcrCommandsBundle.git
    git fetch phpcrcommandsbundle
    git checkout -b phpcrcommandsbundle_branch phpcrcommandsbundle/master
    git checkout master
    git read-tree --prefix=src/Symfony/Cmf/Bundle/PhpcrCommandsBundle -u phpcrcommandsbundle_branch
    git commit -m "imported PhpcrCommandsBundle"

    git remote add corebundle git://github.com/symfony-cmf/CoreBundle.git
    git fetch corebundle
    git checkout -b corebundle_branch corebundle/master
    git checkout master
    git read-tree --prefix=src/Symfony/Cmf/Bundle/CoreBundle -u corebundle_branch
    git commit -m "imported CoreBundle"

    git remote add navigationbundle git://github.com/symfony-cmf/NavigationBundle.git
    git fetch navigationbundle
    git checkout -b navigationbundle_branch navigationbundle/master
    git checkout master
    git read-tree --prefix=src/Symfony/Cmf/Bundle/NavigationBundle -u navigationbundle_branch
    git commit -m "imported NavigationBundle"

    git remote add contentbundle git://github.com/symfony-cmf/ContentBundle.git
    git fetch contentbundle
    git checkout -b contentbundle_branch contentbundle/master
    git checkout master
    git read-tree --prefix=src/Symfony/Cmf/Bundle/ContentBundle -u contentbundle_branch
    git commit -m "imported ContentBundle"

    git remote add multilangcontentbundle git://github.com/symfony-cmf/MultilangContentBundle.git
    git fetch multilangcontentbundle
    git checkout -b multilangcontentbundle_branch multilangcontentbundle/master
    git checkout master
    git read-tree --prefix=src/Symfony/Cmf/Bundle/MultilangContentBundle -u multilangcontentbundle_branch
    git commit -m "imported MultilangContentBundle"
