@extends('layouts.login_master')

@section('content')

<div class="page-content">
    <div class="content-wrapper">
        <div class="content">
            <div class="card">
                <div class="card-header">
                    <h1 class="card-title font-weight-bold text-center">POLITIQUE DE CONFIDENTIALITÉ</h1>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div style="font-size: 16px;" class="col-md-10 offset-md-1">
                            <p>Dernière modification : 4 novembre 2019</p>

                            <h4 class="font-weight-semibold">Introduction</h4>

                            <p>{{ $app_name }} ("Nous") respectons votre vie privée et nous nous engageons à la protéger en conformité avec cette politique.</p>

                            <p>Cette politique décrit les types d'informations que nous pouvons collecter auprès de vous ou que vous pouvez fournir lorsque vous visitez le site web <a target="_blank" href="{{ $app_url }}">{{ $app_url }}</a> (notre "Site web") et nos pratiques en matière de collecte, d'utilisation, de conservation, de protection et de divulgation de ces informations.</p>

                            <p>Cette politique s'applique aux informations que nous collectons :</p>

                            <ul>
                                <li>Sur ce Site web.</li>
                                <li>Dans les courriels, les messages texte et autres communications électroniques entre vous et ce Site web.</li>
                            </ul>

                            <p>Veuillez lire attentivement cette politique pour comprendre nos politiques et pratiques concernant vos informations et comment nous les traiterons. Si vous n'êtes pas d'accord avec nos politiques et pratiques, vous avez le choix de ne pas utiliser notre Site web. En accédant ou en utilisant ce Site web, vous acceptez cette politique de confidentialité. Cette politique peut être modifiée de temps à autre (voir Modifications de notre politique de confidentialité). Votre utilisation continue de ce Site web après les modifications constitue une acceptation de ces changements, veuillez donc vérifier périodiquement la politique pour les mises à jour.</p>

                            <h4 class="font-weight-semibold">Enfants de moins de 13 ans</h4>

                            <p>Nous ne collectons pas sciemment des informations personnelles auprès d'enfants de moins de 13 ans. Si vous avez moins de 13 ans, ne fournissez aucune information sur ce Site web ou sur l'une de ses fonctionnalités sans obtenir au préalable le consentement parental. Si nous apprenons que nous avons collecté ou reçu des informations personnelles d'un enfant de moins de 13 ans sans vérification du consentement parental, nous supprimerons ces informations. Si vous pensez que nous pourrions avoir des informations provenant d'un enfant de moins de 13 ans, veuillez appeler {{ $contact_phone }}</p>

                            <h4 class="font-weight-semibold">Informations que nous collectons vous concernant et comment nous les collectons</h4>

                            <p>Nous collectons plusieurs types d'informations auprès des utilisateurs de notre Site web, y compris :</p>

                            <ul>
                                <li>les informations grâce auxquelles vous pouvez être personnellement identifié ("informations personnelles") ;</li>
                                <li>les informations vous concernant, mais qui ne permettent pas de vous identifier individuellement ; et/ou</li>
                                <li>des informations sur votre connexion Internet, l'équipement que vous utilisez pour accéder à notre Site web et les détails d'utilisation.</li>
                            </ul>

                            <p>Nous collectons ces informations :</p>

                            <ul>
                                <li>Directement auprès de vous lorsque vous nous les fournissez.</li>
                                <li>Automatiquement lorsque vous naviguez sur le site. Les informations collectées automatiquement peuvent inclure des détails d'utilisation, des adresses IP et des informations collectées via des cookies ou d'autres technologies de suivi.</li>
                            </ul>

                            <h4 class="font-weight-semibold">Informations que vous nous fournissez. Les informations que nous collectons sur notre Site web peuvent inclure :</h4>

                            <ul>
                                <li>Les informations que vous fournissez en remplissant des formulaires ou des sondages sur notre Site web. Les informations personnelles soumises ne seront pas transférées à des tiers non affiliés, sauf indication contraire au moment de la collecte. Lorsque vous soumettez des informations personnelles, elles ne sont utilisées que dans le but indiqué au moment de la collecte.</li>
                                <li>Les enregistrements et copies de votre correspondance (y compris les adresses électroniques) si vous nous contactez.</li>
                            </ul>

                            <h4 class="font-weight-semibold">Informations que nous collectons grâce à des technologies de collecte de données automatiques :</h4>

                            <p>Lorsque vous naviguez sur notre Site web et que vous interagissez avec celui-ci, nous pouvons utiliser des technologies de collecte de données automatiques pour recueillir certaines informations sur votre équipement, vos actions de navigation et vos habitudes, y compris :</p>

                            <ul>
                                <li>Les détails de vos visites sur notre Site web, y compris les données de trafic, les données de localisation, les journaux et autres données de communication, ainsi que les ressources auxquelles vous accédez et que vous utilisez sur le Site web.</li>
                                <li>Les informations sur votre ordinateur et votre connexion Internet, y compris votre adresse IP, votre système d'exploitation et le type de navigateur.</li>
                            </ul>

                            <p>Les informations que nous collectons automatiquement sont des données statistiques et peuvent inclure des informations personnelles, que nous pouvons conserver ou associer à des informations personnelles que nous collectons d'autres manières ou que nous recevons de tiers. Cela nous aide à améliorer notre Site web et à fournir un service plus efficace et plus personnalisé, notamment en nous permettant de :</p>

                            <ul>
                                <li>Estimer la taille de notre audience et les modèles d'utilisation.</li>
                                <li>Accélérer vos recherches.</li>
                                <li>Vous reconnaître lorsque vous revenez sur notre Site web.</li>
                            </ul>

                            <p>Les technologies que nous utilisons pour cette collecte automatique de données peuvent inclure :</p>

                            <ul>
                                <li><strong>Cookies</strong> (ou cookies de navigateur). Un cookie est un petit fichier placé sur le disque dur de votre ordinateur. Vous pouvez refuser d'accepter les cookies de votre navigateur en activant le paramètre approprié de votre navigateur. Cependant, si vous sélectionnez ce paramètre, certaines parties de notre Site web pourraient être inaccessibles ou ne pas fonctionner correctement. À moins que vous n'ayez modifié les paramètres de votre navigateur pour qu'il refuse les cookies, notre système émettra des cookies lorsque vous dirigerez votre navigateur vers notre Site web.</li>
                                <li><strong>Cookies Flash.</strong> Certaines fonctionnalités de notre Site web peuvent utiliser des objets stockés localement (ou cookies Flash) pour recueillir et stocker des informations sur vos préférences et votre navigation vers, depuis et sur notre Site web. Les cookies Flash ne sont pas gérés par les mêmes paramètres de navigateur que ceux utilisés pour les cookies de navigateur. Pour plus d'informations sur la gestion de vos paramètres de confidentialité et de sécurité pour les cookies Flash, consultez la page des paramètres du lecteur Flash sur le site web d'Adobe. </li>
                                <li><strong>Balises Web.</strong> Les pages de notre Site web peuvent contenir de petits fichiers électroniques connus sous le nom de balises Web (également appelées gifs transparents, tags de pixels et gifs d'un pixel) qui nous permettent, par exemple, de compter les utilisateurs qui ont visité ces pages et d'autres statistiques liées au site web (par exemple, enregistrer la popularité de certains contenus du site web et vérifier l'intégrité du système et du serveur).</li>
                            </ul>

                            <h4 class="font-weight-semibold">Comment nous utilisons vos informations</h4>

                            <p>Nous utilisons les informations que nous collectons vous concernant ou que vous nous fournissez, y compris toute information personnelle :</p>

                            <ul>
                                <li>Pour vous présenter notre Site web et son contenu.</li>
                                <li>Pour vous permettre de participer à des fonctionnalités interactives sur notre Site web.</li>
                                <li>De toute autre manière que nous pouvons décrire lorsque vous fournissez les informations.</li>
                                <li>Pour toute autre fin avec votre consentement.</li>
                            </ul>

                            <h4 class="font-weight-semibold">Divulgation de vos informations</h4>

                            <p>Nous pouvons divulguer des informations agrégées sur nos utilisateurs, ainsi que des informations qui n'identifient aucun individu, sans restriction.</p>

                            <p>Nous pouvons divulguer les informations personnelles que nous collectons ou que vous nous fournissez comme décrit dans cette politique de confidentialité :</p>

                            <ul>
                                <li>Pour remplir l'objectif pour lequel vous les avez fournies.</li>
                                <li>Pour toute autre fin divulguée par nous lorsque vous fournissez les informations.</li>
                                <li>Avec votre consentement.</li>
                            </ul>

                            <p>Nous pouvons également divulguer vos informations personnelles :</p>

                            <ul>
                                <li>Pour nous conformer à toute ordonnance judiciaire, loi ou procédure légale, y compris pour répondre à toute demande gouvernementale ou réglementaire.</li>
                                <li>Pour faire respecter ou appliquer nos <a target="_blank" href="{{ route('terms_of_use') }}">Conditions d'utilisation</a>.</li>
                                <li>Si nous estimons que la divulgation est nécessaire ou appropriée pour protéger nos droits, notre propriété ou la sécurité de nos étudiants ou d'autres personnes.</li>
                            </ul>

                            <h4 class="font-weight-semibold">Vos choix concernant la manière dont nous utilisons et divulguons vos informations</h4>
                            <p>Nous nous efforçons de vous offrir des choix concernant les informations personnelles que vous nous fournissez. Nous avons mis en place des mécanismes pour vous offrir le contrôle suivant sur vos informations :</p>

                            <ul>
                                <li><strong>Technologies de suivi et publicité</strong>. Vous pouvez configurer votre navigateur pour refuser tous les cookies ou certains cookies de navigateur, ou pour vous alerter lorsque des cookies sont envoyés. Pour savoir comment gérer vos paramètres de cookies Flash, consultez la page des paramètres du lecteur Flash sur le site web d'Adobe. Si vous désactivez ou refusez les cookies, veuillez noter que certaines parties de ce site peuvent être inaccessibles ou ne pas fonctionner correctement.</li>
                            </ul>

                            <h4 class="font-weight-semibold">Sécurité des données</h4>

                            <p>Nous avons mis en place des mesures visant à sécuriser vos informations personnelles contre toute perte accidentelle et tout accès, utilisation, altération et divulgation non autorisés.</p>

                            <h4 class="font-weight-semibold">Modifications de notre politique de confidentialité</h4>

                            <p>Notre politique est de publier toute modification apportée à notre politique de confidentialité sur cette page. Si nous apportons des modifications importantes à la manière dont nous traitons les informations personnelles de nos utilisateurs, nous vous en informerons par une notification sur la page d'accueil du Site web. La date de la dernière révision de la politique de confidentialité est indiquée en haut de la page. Vous êtes responsable de consulter périodiquement notre Site web et cette politique de confidentialité pour vérifier s'il y a des modifications.</p>

                            <h4 class="font-weight-semibold">Coordonnées</h4>

                            <p>Pour poser des questions ou faire des commentaires sur cette politique de confidentialité et sur nos pratiques en matière de confidentialité, veuillez appeler {{ $contact_phone }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
