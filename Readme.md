# Tp Symfony

## création base de données 

voir les fichiers :
 - modeleBdd.png
 - modeleBdd.mwb

## création des entitées

### création de l'user avec : 

```
symfony console make:user
```
permet la création d'une entité user avec une propriété unique, ici sur username

### création des autres entitées : 
pour créer toutes les autres entitées nécessaire pour correspondre à notre modèle de base de données :
```
symfony console make:entity
```
cette commande est également utilisée pour modifier les entitées (ex : User) ou encore pour les relier entre elles.

## création base de donnnées dans phpmyadmin

on configure dans notre .env les informations nécessaire à doctine, pour ma part :
```
 DATABASE_URL="mysql://root:@127.0.0.1:3306/TpSymfony?serverVersion=5.7"
 ```

une fois toute nos entités crées et reliées entre elles, on peut créer un base de donnée avec doctrine :
```
symfony console doctrine:database:create
```
Pour ce Tp, c'est utile pour des tests, mais également visualiser que nous avons bien les bonnes relations entre nos tables dans le concepteur

## création d'une version de la bdd :
avec cette commande, on créer un version de notre bdd avec 2 utilisateur.
```
symfony console doctrine:migrations:dump-schema
```

### utilisateur :
username : admin
password : 123456

username : test
password : 123456


## gestion des utilisateurs

### création de l'authentification :
```
symfony console make:auth
```
création du fichier Authenticator.php dans le dossier /src/Security/ 

création de la vue login.html.twig dans le dossier /templates/security/ pour permêtre aux hôtes, hôtesse de
logguer les invitées ou à l'administrateur de se connecter et ainsi pouvoir gérer l'application.


### création du formulaire de registration avec : 
```
symfony console make:registration-form
```
Créer un formulaire d'autentification, afin que l'admin puisse créer des nouveau user.

#### après avoir créer un utilisateur admin :

ET lui avoir donner le role "ROLE_ADMIN" directement depuis phpmyadmin

```php
#[Route('/admin')]
class RegistrationController extends AbstractController
```
on ajout '/admin' à la route du RegistrationController pour ensuite bloqué son accès à toute personne qui n'est pas admin.
```yaml
access_control:
         - { path: ^/admin, roles: ROLE_ADMIN }
```
pour contrôler l'accès au chemin commençant par /admin,  on définit l'acces_control comme ci-dessus dans le fichier security.yaml situé dans le dossier /config/packages/

## récupération des tables liées

on ajoute dans les entitées type et artiste :
```php
public function __toString(): string
    {
        return $this->getNom();
    }
```

Pour les récupérer dans le formulaire de l'entité Oeuvre.

Egalement dans les entitées User, Lieu et Oeuvre, pour les récupérer dans le formulaire de l'entité Evenement.

## gestion des images

on stock l'endroit où on va stoker les images dans le fichier /config/services.yaml : 
```yaml
parameters:
    files: '%kernel.project_dir%/public/uploads/images'
```

dans le controller, pour ajouter une image lorsque l'on créer un artiste
```php
$artiste = $form->getData();
$image = $form->get('image')->getData();

if($image){
    $orginalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
    $newFile = $sluggerInterface->slug($orginalName). '-'.uniqid().'.'.$image->guessExtension();
    try{
        $image->move(
            $this->getParameter('files'),
            $newFile
        );

    }catch(FileException $e){
        throw new \Exception($e);
    }
    $artiste->setImage($newFile);
}
```

pour éditer un artiste, j'ai repris le code ci-dessus, et j'ai ajouter le suivant pour changer l'image et la supprimer de notre dossier où les stock :
```php
 if ($previousImage != $newImage && $previousImage !="") {
    $racine = $this->getParameter('files');
    // dd($this->getParameter('files'));
    $completePath = $racine .'/'. $previousImage;
    unlink($completePath);
}
```

et enfin, dans la fonction delete, j'ajoute juste : 
```php
if (is_file($completePath)) {

    unlink($completePath);
}
```

Il faudrait créer un service pour éviter de copier ces morceaux de code dans les autres controllers qui vont gérer des images.

Comme le Tp est sur un seul jour, je vais essayer que tout fonctionne et si j'ai le temps je me pencherais sur le service.


## rendu en JSON

c'est prévu dans le OeuvreController avec les routes :
```
/oeuvres
/oeuvre/{id}
```
mais problèmes avec les références circulaires.

ça fait une piste à suivre pour s'améliorer en symfony, mais là, j'ai pas trop le temps.

## tentative d'un service

avec une fonction bid($prix) pour enchérir,
mais s'en rendu j'ai du mal à l'utiliser.

Une autre fois avec plus de temps.

## front-end

### sidebar

création d'un partials pour la sidebar, on l'inclue ensuite dans la base
```twig
{% include "partials/_sidebar.html.twig" %}
```

### Bootstrap form

```
form_themes: ['bootstrap_5_layout.html.twig']
```
Ajout de cette ligne dans /config/packages/twig.yaml.

Permet d'avoir des formulaire bootstrap dans l'ensemble de l'application
Pour avoir un minimum de CSS pour l'admin.

# récupération des éléments important de l'énoncé. Inutile au correcteur

récupérer des informations sur les œuvres exposées

 sélectionner une œuvre sur la tablette et
ainsi pouvoir récupérer un maximum d’informations la concernant

les hôtes et hôtesses de
l’événement devront se charger de logger l’utilisateur avant de lui remettre sa tablette

un invité doit être capable de soumettre une
offre sur l'œuvre depuis sa tablette. Le fonctionnement se rapproche alors d’un système d’enchères

Toutes les œuvres devront avoir un prix de départ

à partir du moment où une personne définit un prix supérieur, ce dernier devient le
nouveau prix de référence et sera donc celui qui sera affiché sur la tablette des invités. on saura ainsi
quel est le prix actuel de l'œuvre.

Les enchères sont anonymes. Si le prix le plus élevé actuel doit être vu de tous, on n'affiche jamais les
infos concernant l'acquéreur potentiel.

Votre client, de son côté, souhaite pouvoir gérer ses événements, créer des fiches d'œuvres, créer
des invités, les inscrire à un événement etc...

Vous devrez pouvoir créer des invités, les assigner à un événement, créer les fiches des œuvres qui
seront exposées lors de cet événement, déposer des enchères sur des œuvres…

il est important de sécuriser ce panel d’administration par login / mot de passe.

Vous devrez donc exposer plusieurs endpoints (routes) qui seront utilisés par l’application pour
récupérer les infos dont elle aura besoin.
● récupérer toutes les oeuvres
● récupérer les infos d’une oeuvre en particulier

Ces informations devront êtres retournées sous forme de JSON

Si vous avez encore du temps, vous pouvez penser à un système d’authentification par web token
pour l’application

