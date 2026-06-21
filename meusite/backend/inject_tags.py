#!/usr/bin/env python3
import os
import sys
import re
import argparse

# Configuração dos arquivos
FRONTEND_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
PUBLIC_PAGES = [
    "404.html", "automacao.html", "contato.html", "faq.html", "googleads.html",
    "index.html", "politica-privacidade.html", "psite1.html", "psite2.html",
    "psite3.html", "psite4.html", "psite5.html", "psite6.html", "psite7.html",
    "psite8.html", "psite9.html", "psite10.html", "seo.html", "sites.html",
    "termosdeservico.html", "tiktok.html", "totem.html", "webar.html"
]

def make_gtm_head_script(gtm_id):
    return f"""<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){{w[l]=w[l]||[];w[l].push({{'gtm.start':
new Date().getTime(),event:'gtm.js'}});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
}})(window,document,'script','dataLayer','{gtm_id}');</script>
<!-- End Google Tag Manager -->"""

def make_gtm_body_script(gtm_id):
    return f"""<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={gtm_id}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->"""

def make_ga4_head_script(ga4_id):
    return f"""<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={ga4_id}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){{dataLayer.push(arguments);}}
  gtag('js', new Date());
  gtag('config', '{ga4_id}');
</script>
<!-- End Global site tag -->"""

def make_conversions_tracker():
    return """<!-- Cyborg Automated Conversions Tracker -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  // 1. WhatsApp Clicks Tracker
  document.addEventListener('click', function(e) {
    var target = e.target.closest('a');
    if (target && target.href) {
      var href = target.href.toLowerCase();
      if (href.indexOf('wa.me') !== -1 || href.indexOf('whatsapp.com') !== -1 || href.indexOf('phone=') !== -1) {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
          'event': 'click_whatsapp',
          'whatsapp_url': target.href
        });
        console.log('[Cyborg Tracker] WhatsApp click captured:', target.href);
      }
    }
  });

  // 2. Form Submissions Tracker
  document.addEventListener('submit', function(e) {
    var form = e.target;
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
      'event': 'lead_conversion',
      'form_id': form.id || form.className || 'unnamed_form',
      'form_action': form.action || ''
    });
    console.log('[Cyborg Tracker] Lead form submission captured:', form.id || form.className);
  });
});
</script>
<!-- End Cyborg Automated Conversions Tracker -->"""

def clean_existing_tags(html):
    # Remove blocos antigos do GTM do <head>
    html = re.sub(r'<!-- Google Tag Manager -->.*?<!-- End Google Tag Manager -->', '', html, flags=re.DOTALL)
    # Remove noscript antigos do GTM do <body>
    html = re.sub(r'<!-- Google Tag Manager \(noscript\) -->.*?<!-- End Google Tag Manager \(noscript\) -->', '', html, flags=re.DOTALL)
    # Remove blocos antigos do Google Analytics / gtag.js
    html = re.sub(r'<!-- Global site tag \(gtag\.js\) - Google Analytics -->.*?<!-- End Global site tag -->', '', html, flags=re.DOTALL)
    html = re.sub(r'<!-- Google Ads Conversion Tag -->.*?<!-- End Google Ads Conversion Tag -->', '', html, flags=re.DOTALL)
    # Remove tracker antigo da Cyborg
    html = re.sub(r'<!-- Cyborg Automated Conversions Tracker -->.*?<!-- End Cyborg Automated Conversions Tracker -->', '', html, flags=re.DOTALL)
    return html

def inject_tags(file_name, gtm_id, ga4_id):
    fpath = os.path.join(FRONTEND_DIR, file_name)
    if not os.path.exists(fpath):
        print(f"[WARN] Arquivo nao encontrado: {file_name}")
        return False
        
    try:
        with open(fpath, "r", encoding="utf-8") as f:
            html = f.read()
            
        # Limpa as tags antigas para evitar duplicidade
        html_clean = clean_existing_tags(html)
        
        # Constrói os scripts a injetar
        head_scripts = []
        is_gtm_active = gtm_id and gtm_id.lower() not in ["", "none", "false", "null"]
        is_ga4_active = ga4_id and ga4_id.lower() not in ["", "none", "false", "null"]
        
        if is_gtm_active:
            head_scripts.append(make_gtm_head_script(gtm_id))
        if is_ga4_active:
            head_scripts.append(make_ga4_head_script(ga4_id))
            
        # Sempre injeta o rastreador de conversões automatizado da Cyborg
        head_scripts.append(make_conversions_tracker())
            
        head_scripts_str = "\n".join(head_scripts)
        
        # Injeta no <head>
        if head_scripts_str:
            if "<head>" in html_clean:
                html_clean = html_clean.replace("<head>", f"<head>\n{head_scripts_str}")
            elif "</head>" in html_clean:
                html_clean = html_clean.replace("</head>", f"{head_scripts_str}\n</head>")
            else:
                print(f"[ERROR] Nao foi possivel achar a tag <head> em {file_name}")
                return False
                
        # Injeta no <body> (somente o noscript do GTM)
        if is_gtm_active:
            body_script = make_gtm_body_script(gtm_id)
            if "<body>" in html_clean:
                html_clean = html_clean.replace("<body>", f"<body>\n{body_script}")
            elif "<body " in html_clean:
                # Caso a tag body tenha atributos (ex: class, id)
                match = re.search(r'<body[^>]*>', html_clean)
                if match:
                    body_tag = match.group(0)
                    html_clean = html_clean.replace(body_tag, f"{body_tag}\n{body_script}")
            else:
                print(f"[ERROR] Nao foi possivel achar a tag <body> em {file_name}")
                return False
                
        # Salva o arquivo modificado
        with open(fpath, "w", encoding="utf-8") as f:
            f.write(html_clean)
            
        print(f"[OK] Tags injetadas com sucesso em: {file_name}")
        return True
    except Exception as e:
        print(f"[ERROR] Erro ao modificar {file_name}: {e}")
        return False

def main():
    parser = argparse.ArgumentParser(description="Injetor de tags Google Tag Manager e Google Analytics para Agencia Cyborg")
    parser.add_argument("--gtm", type=str, default="GTM-CYBORG7", help="ID do Google Tag Manager (ex: GTM-XXXXXXX)")
    parser.add_argument("--ga4", type=str, default="G-CYBORG77", help="ID do Google Analytics 4 (ex: G-XXXXXXXXXX)")
    args = parser.parse_args()
    
    print(f"[INFO] Iniciando injecao automatica de tags no diretorio: {FRONTEND_DIR}")
    print(f"[INFO] Usando GTM ID: {args.gtm}")
    print(f"[INFO] Usando GA4 ID: {args.ga4}")
    print("----------------------------------------------------------------------")
    
    success_count = 0
    for page in PUBLIC_PAGES:
        if inject_tags(page, args.gtm, args.ga4):
            success_count += 1
            
    print("----------------------------------------------------------------------")
    print(f"[SUCCESS] Processo concluido! Sincronizadas {success_count}/{len(PUBLIC_PAGES)} paginas com sucesso.")

if __name__ == "__main__":
    main()
