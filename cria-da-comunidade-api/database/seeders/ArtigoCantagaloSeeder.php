<?php

namespace Database\Seeders;

use App\Models\Artigo;
use App\Models\Comunidade;
use Illuminate\Database\Seeder;

class ArtigoCantagaloSeeder extends Seeder
{
    public function run(): void
    {
        $comunidade = Comunidade::first();

        $corpo = <<<HTML
<h2>Uma comunidade entre dois mundos</h2>
<p>O Morro do Cantagalo fica encravado entre os bairros de Ipanema e Copacabana, na Zona Sul do Rio de Janeiro. Do alto de suas vielas, dá para ver o mar, a Lagoa Rodrigo de Freitas e o agito da cidade que nunca para. Mas dentro da favela, o tempo tem seu próprio ritmo — feito de samba, jiu-jítsu, baile funk e resistência.</p>

<p>Com cerca de 7 mil moradores, o Cantagalo forma junto com o Pavão e o Pavãozinho uma das comunidades mais emblemáticas do Rio de Janeiro. É dali que saíram histórias que atravessaram fronteiras e colocaram o nome da favela no mundo inteiro.</p>

<h2>Fernando Terere — O Mito do Jiu-Jítsu</h2>
<img src="https://assets.dev-filo.dift.io/img/2023/03/10/fer2120_sq.png" alt="Fernando Terere" style="width:100%;max-width:480px;border-radius:12px;margin:16px auto 24px;display:block;object-fit:cover;">
<p>Fernando Augusto Vieira, o <strong>Terere</strong>, nasceu no Cantagalo e se tornou um dos maiores lutadores de jiu-jítsu de todos os tempos. Campeão Mundial e Pan-Americano múltiplas vezes, Terere desenvolveu um estilo único de luta — criativo, explosivo e imprevisível — que influenciou toda uma geração de atletas no Brasil e no mundo.</p>

<p>Sua trajetória é marcada pela superação. Em meio a dificuldades pessoais e momentos difíceis, ele nunca abandonou o Cantagalo. Voltou para a comunidade, montou projetos sociais e hoje é referência não só dentro do tatame, mas como símbolo de recomeço e compromisso com a quebrada que o criou. Para os meninos do morro, Terere provou que o jiu-jítsu pode ser muito mais do que um esporte — pode ser um caminho.</p>

<h2>Finfou — Criatividade e Garra no Tatame</h2>
<img src="https://www.bjjheroes.com/wp-content/uploads/2014/06/Alan-Findou.jpg" alt="Finfou" style="width:100%;max-width:480px;border-radius:12px;margin:16px auto 24px;display:block;object-fit:cover;">
<p><strong>Finfou</strong> é outro filho do Cantagalo que encontrou no jiu-jítsu seu destino. Atleta de alto nível, representou a comunidade em competições nacionais e internacionais, carregando a bandeira da favela onde quer que fosse. Seu jogo criativo e sua dedicação dentro e fora das academias o tornaram referência para os jovens da comunidade.</p>

<p>Junto com Terere, Finfou faz parte de uma geração que provou ao mundo que a excelência esportiva não tem endereço. O Cantagalo é, até hoje, um celeiro de talentos do jiu-jítsu brasileiro — e isso não é por acaso. É fruto de muita luta, treino e amor pela arte suave.</p>

<h2>Bezerra da Silva — O Rei do Partido Alto</h2>
<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bezerra_%285455933796%29.jpg/960px-Bezerra_%285455933796%29.jpg" alt="Bezerra da Silva" style="width:100%;max-width:480px;border-radius:12px;margin:16px auto 24px;display:block;object-fit:cover;">
<p>Antes de qualquer outro, havia <strong>Bezerra da Silva</strong>. O sambista pernambucano de nascença mas carioca de alma viveu no Cantagalo e eternizou a voz e a sabedoria da favela em suas músicas. Com letras que misturavam humor afiado, crítica social e o cotidiano das comunidades, Bezerra se tornou o maior porta-voz dos morros do Rio de Janeiro.</p>

<p>Sucessos como <em>Acusado Injustamente</em>, <em>Eu Sou Favela</em> e <em>Malandro é Malandro e Mané é Mané</em> ainda ecoam pelas vielas do Cantagalo e de todas as favelas do Brasil. Bezerra faleceu em 2005, mas sua música permanece viva e presente — a voz de um povo que sempre existiu, sempre resistiu e sempre cantou sua própria história.</p>

<blockquote>Eu sou favela, orgulho de onde eu vim. Quem não gosta que se explique, a favela tá aqui. — Bezerra da Silva</blockquote>

<h2>MC Cabelinho — A Nova Geração do Funk</h2>
<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/56/MC_Cabelinho_-_TVZ_2023_%281%29.png/500px-MC_Cabelinho_-_TVZ_2023_%281%29.png" alt="MC Cabelinho" style="width:100%;max-width:480px;border-radius:12px;margin:16px auto 24px;display:block;object-fit:cover;">
<p>Do Cantagalo também saiu <strong>MC Cabelinho</strong>, um dos nomes mais importantes do funk e do rap contemporâneo brasileiro. Com uma voz marcante, presença única e letras que falam de amor, favela, perda e superação, Cabelinho conquistou o Brasil inteiro e chamou a atenção de toda a cena musical.</p>

<p>Seu trabalho foi aclamado pela crítica e o colocou entre os artistas mais relevantes da nova geração. Cabelinho prova que o Cantagalo continua produzindo talentos capazes de dialogar com o mundo inteiro sem abrir mão de suas raízes. Cada música dele é um retrato da comunidade que o formou.</p>

<h2>Mestre Cláudio Coelho e a Academia Nobre Arte</h2>
<img src="https://upload.wikimedia.org/wikipedia/commons/8/8e/Gr%C3%A3o-Mestre_do_Boxe_Claudio_Coelho.jpg" alt="Mestre Cláudio Coelho - Nobre Arte" style="width:100%;max-width:480px;border-radius:12px;margin:16px auto 24px;display:block;object-fit:cover;">
<p>Se o Cantagalo é uma referência no esporte, muito disso se deve ao <strong>Mestre Cláudio Coelho</strong>. Há mais de 40 anos à frente da Academia de Boxe <strong>Nobre Arte</strong>, localizada no alto do morro, Claudinho Coelho transformou um espaço simples na favela em um dos centros de artes marciais mais respeitados do Brasil.</p>

<p>A Nobre Arte foi pioneira em algo raro no esporte brasileiro dos anos 90: receber sob o mesmo teto rivais históricos do Jiu-Jítsu e da Luta-Livre, no auge da guerra entre as modalidades. Claudinho Coelho convidou nomes como Marco Ruas, Amaury Bitetti, Murilo Bustamante e Zé Mario Sperry para treinar na academia — um gesto de neutralidade e visão que fez da Nobre Arte o espaço mais democrático do Vale-Tudo nacional.</p>

<p>Presidente da academia, autor de dois livros sobre boxe e criador do projeto social <em>Meninos do Boxe</em>, Cláudio Coelho usa o esporte como ferramenta de transformação para crianças e jovens da comunidade. Para o morro, a Nobre Arte não é só uma academia — é uma escola de vida.</p>

<h2>O Cantagalo que resiste e inspira</h2>
<p>Cada um à sua maneira, Terere, Finfou, Bezerra da Silva, MC Cabelinho e Mestre Cláudio Coelho representam o que há de mais poderoso no Cantagalo: a capacidade de transformar a adversidade em arte, em esporte, em luta e em vida.</p>

<p>A favela não é só o lugar onde eles nasceram. É o combustível que os fez chegar onde chegaram. É a identidade que carregam com orgulho. E é por isso que, independente de onde estejam, o Cantagalo está sempre presente em cada vitória, em cada nota musical, em cada ippon, em cada verso e em cada conquista coletiva.</p>

<p>O Cantagalo é maior do que qualquer história individual. É feito de todas elas juntas.</p>
HTML;

        Artigo::updateOrCreate(
            ['slug' => 'historia-morro-cantagalo-personagens'],
            [
                'comunidade_id'  => $comunidade?->id,
                'titulo'         => 'A História do Morro do Cantagalo e seus Grandes Personagens',
                'slug'           => 'historia-morro-cantagalo-personagens',
                'resumo'         => 'Do alto do Cantagalo, entre Ipanema e Copacabana, uma comunidade que produziu campeões de jiu-jítsu, ícones do samba e do funk, e lideranças que mudaram o Rio de Janeiro.',
                'corpo'          => $corpo,
                'imagem_capa_url'=> 'https://assets.dev-filo.dift.io/img/2023/03/10/fer2120_sq.png',
                'categoria'      => 'Cultura',
                'autor'          => 'Redação Cria da Comunidade',
                'publicado'      => true,
                'publicado_em'   => now(),
            ]
        );
    }
}
