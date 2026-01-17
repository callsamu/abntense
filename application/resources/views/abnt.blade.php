#set text(font: "Liberation Serif", size: 12pt)

#set page(margin: (
    top: 3cm,
    left: 3cm,
    right: 2cm,
    bottom: 2cm,
))

#set par(
    justify: true,
    first-line-indent: 1.25cm,
    leading: 0.7811699164em,
)

#let space = (n) => {
    for i in range(n) { linebreak() }
}

#page(
    align(
        center,
        text[
            #upper[*{{ $institution }}*] \
            #upper[*Bacharelado em Engenharia da Computação*]
            #space(3)
            #upper[{{ $authors }}] \
            #space(16)
            #upper[*{{ $title }}*] \
            Subtitulo do Trabalho \
            #space(16)
            #upper[{{ $location }}] \
            {{ $year }}
        ],
    )
)

#set text(top-edge: 0.7em, bottom-edge: -0.3em)

#let pretextual = (nome, texto) => {
    align(center,
        heading(outlined: false, upper(text(weight: "bold", nome))),
    )
    linebreak()
    par(texto)
    pagebreak()
}

{!! $content !!}
