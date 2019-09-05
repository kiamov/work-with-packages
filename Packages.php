<?php
declare(strict_types=1);

class BadArgumentsException extends Exception {
    public function __construct($message) {
      parent::__construct($message);
    }
}

class PackageDependsOnItselfException extends Exception {
    public function __construct($message) {
        parent::__construct($message);
    }
}

class PackageNotFoundException extends Exception {
    public function __construct($message) {
        parent::__construct($message);
    }
}

class NullArgumentsException extends Exception {
    public function __construct($message) {
        parent::__construct($message);
    }
}

class WorkWithPackages
{
    public $packages;

    public function __construct($packages)
    {
        $this->packages = $packages;
    }

    public function PackageInstallation()
    {
        $this->CheckingPackages($this->packages);

        $count_dep = array();
        foreach ($this->packages as $key => $row)
        {
            $count_dep[$key] = count($row['dependencies']);
        }
        array_multisort($count_dep, SORT_ASC, $this->packages);

        for ($i=0; $i < count($this->packages); $i++)
        echo "Package " . $this->packages[$i]['name'] . " was installed.\n";
    }

    public function CheckingPackages($packages)
    {
        try {
            for ($i=0; $i < count($packages); $i++) {
                if (!array_key_exists("name", $packages[$i]) or !array_key_exists("dependencies", $packages[$i])) {
                    throw new BadArgumentsException("Argument name or dependencies not found.");
                }

                if ($packages[$i]["name"] == "") {
                    throw  new NullArgumentsException("Name package with id {$i} is null.");
                }

                for ($j=0; $j < count($packages[$i]["dependencies"]); $j++) {
                    if ($packages[$i]["dependencies"][$j] == $packages[$i]["name"]) {
                        throw new PackageDependsOnItselfException("The package {$packages[$i]["name"]} depends on itself.");
                    }
                    if ($this->CheckForExistence($packages[$i]["dependencies"][$j], $packages) == false) {
                        throw new PackageNotFoundException("Dependence {$packages[$i]["dependencies"][$j]} for package {$packages[$i]["name"]} not found.");
                    }
                }
            }
        } catch (BadArgumentsException $th) {
            echo $th->getMessage();
        } catch (PackageDependsOnItselfException $th) {
            echo $th->getMessage();
        } catch (PackageNotFoundException $th) {
            echo $th->getMessage();
        } catch (NullArgumentsException $th) {
            echo  $th->getMessage();
        }
    }

    public function CheckForExistence($package, $packages)
    {
        $a = 0;
        for ($i=0; $i < count($packages); $i++) {
            if ($packages[$i]["name"] == $package) {
                $a++;
            }
        }
        if ($a == 0) {
            return false;
        } else return true;
    }
}

$p = new WorkWithPackages([
    ["name" => "A", "dependencies" => ["C", "B"]],
    ["name" => "B", "dependencies" => ["A"]],
    ["name" => "C", "dependencies" => []],
    ["name" => "D", "dependencies" => ["B", "C"]],
    ["name" => "E", "dependencies" => ["B", "A", "C"]]
]);
$p->PackageInstallation();