require 'ruble'

bundle 'PHP' do |bundle|
  bundle.author = 'Dreiwerken GmbH'
  bundle.contact_email_rot_13 = ''
  bundle.description =  ''
  start_folding = /(\/\*|\{\s*$|<<<HTML)/
  end_folding = /(\*\/|^\s*\}|^HTML;)/
  bundle.folding['source.php'] = start_folding, end_folding

end


#Code Snippets
#########################################################################################

snippet 'DebugLogger' do |s| 
    s.trigger = 'debug'
    s.expansion  = '\\jamwork\\debug\\DebugLogger::getInstance()->log(${1:output});'
end





#########################################################################################




# Special ENV vars for PHP scope
env 'source.php' do |e|
  e['TM_COMMENT_START'] = '// '
  e.delete('TM_COMMENT_END')
  e['TM_COMMENT_START_2'] = '# '
  e.delete('TM_COMMENT_END_2')
  e['TM_COMMENT_START_3'] = '/* '
  e['TM_COMMENT_END_3'] = '*/'
end
