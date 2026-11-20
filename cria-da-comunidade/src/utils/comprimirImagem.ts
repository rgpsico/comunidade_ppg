/**
 * Reduz uma foto antes do envio: foto de celular tem 3–8 MB e o servidor aceita até 2 MB por arquivo.
 * Redimensiona para no máximo `lado` px no maior lado e salva em JPEG. Se o navegador não conseguir
 * ler a imagem (ex.: HEIC em alguns navegadores), devolve o arquivo original.
 */
export async function comprimirImagem(arquivo: File, lado = 1600, qualidade = 0.82): Promise<File> {
  if (!arquivo.type.startsWith('image/') || arquivo.type === 'image/gif') return arquivo

  try {
    const bitmap = await createImageBitmap(arquivo)
    const escala = Math.min(1, lado / Math.max(bitmap.width, bitmap.height))
    const largura = Math.round(bitmap.width * escala)
    const altura = Math.round(bitmap.height * escala)

    const canvas = document.createElement('canvas')
    canvas.width = largura
    canvas.height = altura
    const ctx = canvas.getContext('2d')
    if (!ctx) return arquivo
    ctx.drawImage(bitmap, 0, 0, largura, altura)
    bitmap.close?.()

    const blob = await new Promise<Blob | null>(res => canvas.toBlob(res, 'image/jpeg', qualidade))
    if (!blob || blob.size >= arquivo.size) return arquivo

    const nome = arquivo.name.replace(/\.[^.]+$/, '') + '.jpg'
    return new File([blob], nome, { type: 'image/jpeg', lastModified: Date.now() })
  } catch {
    return arquivo
  }
}
