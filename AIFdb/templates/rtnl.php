<?php
    header('Content-Type: text/plain');
    header("Content-Transfer-Encoding: Binary"); 
    header("Content-disposition: attachment; filename=\"nodeset" . $template_extra['nodeset'] . ".rtnl\"");
?>
#Rationale Version 160.66 can_be_loaded_by 120.53 uses_features_from 140.56
#-- Start of Prelude --
Node = None
Views = []
Loading = True
ScratchpadTextSupported = CurrentFileFormat.IsLaterThan(130, 55)
PictureSupported = CurrentFileFormat.IsLaterThan(140, 56)
TagsSupported = CurrentFileFormat.IsLaterThan(140, 57)
TypeSupported = CurrentFileFormat.IsLaterThan(140, 60)
EditPromptSupported = CurrentFileFormat.IsLaterThan(140, 57)
ShowOutlineSupported = CurrentFileFormat.IsLaterThan(140, 57)
LockHeadingSupported = CurrentFileFormat.IsLaterThan(140, 58)
TwoOrientationsSupported = CurrentFileFormat.IsLaterThan(140, 59)
HideChildrenSupported = CurrentFileFormat.IsLaterThan(150, 60)
MinimizeNodeSupported = CurrentFileFormat.IsLaterThan(150, 60)
HyperlinkSupported = CurrentFileFormat.IsLaterThan(150, 60)
RelativeLinksSupported = CurrentFileFormat.IsLaterThan(150, 64)
EdgeStyleSupported = CurrentFileFormat.IsLaterThan(160, 61)
BoxStyleSupported = CurrentFileFormat.IsLaterThan(160, 61)
FontSizeSupported = CurrentFileFormat.IsLaterThan(160, 61)
NodeNotesSupported = CurrentFileFormat.IsLaterThan(160,62)
SeparationSupported = CurrentFileFormat.IsLaterThan(160,63)
JumbleMapSupported = CurrentFileFormat.IsLaterThan(160, 65)
EssayTypeSupported = CurrentFileFormat.IsLaterThan(160, 65)

if not CurrentFileFormat.IsLaterThan(130, 54):
   morphTarget = None

def Create(type):
   global Node
   if morphTarget == None:
     Node = app.Create(type)
   else:
     Morph(morphTarget, type)
     Node = morphTarget
   return Node 

def CreateChild(parent, type):
   global Node
   Node = app.CreateChild(parent, type)
   return Node

def CreateAnnotation(parent, type):
   global Node
   Node = app.CreateAnnotation(parent, type)
   return Node

def SetText(text):
   app.SetText(Node, text)

def SetTag(key, value):
   if TagsSupported:
      app.SetTag(Node, key, value)

def SetCustomType(group, type):
   if TypeSupported:      
      Node.CustomGroup = group
      Node.CustomType = type

def SetEditPrompt(text):
   if EditPromptSupported:
      app.SetEditPrompt(Node, text)
   else:
      AddMissingFeature('helpful hint')
     
def SetEvaluation(evaluation):
   app.SetEvaluation(Node, evaluation)

def SetTint(tint):
   app.SetTint(Node, tint)

def SetImageKey(key):
   if PictureSupported:
      app.SetImageKey(Node, key)
   else:
      AddMissingFeature('picture')

def SetHyperlink(relativeLink, link, description, openExternally):
   if HyperlinkSupported:
      if RelativeLinksSupported:
         app.SetHyperlink(Node, relativeLink, link, description, openExternally)
      else:
         app.SetHyperlink(Node, link, description, openExternally)
   else:
      AddMissingFeature('Hyperlink') 

def SetEdgeStyle(layout, headStyle, tailStyle, spreadChildEdges):
   if EdgeStyleSupported:
      app.SetEdgeStyle(Node, layout, headStyle, tailStyle, spreadChildEdges)
   else:
      AddMissingFeature('Experimental Pro feature -- custom edge style')

def SetBoxStyle(outlineStyle, penWidth, outlineRgbVal):
   if BoxStyleSupported:
      app.SetBoxStyle(Node, outlineStyle, penWidth, outlineRgbVal, True, 0x000000)
   else:
      AddMissingFeature('Experimental Pro feature -- custom box style')

def SetBoxOutline(style, penWidth, rgbVal, showOutline):
   if BoxStyleSupported:
      app.SetBoxOutline(Node, style, penWidth, rgbVal, showOutline)
   else:
      AddMissingFeature('Experimental Pro feature -- custom box outline style')

def SetBoxFill(rgbVal, showFill):
   if BoxStyleSupported:
      app.SetBoxFill(Node, rgbVal, showFill)
   else:
      AddMissingFeature('Experimental Pro feature -- custom box fill style')

def SetFontSizeDelta(delta):
   if FontSizeSupported:
      app.SetFontSizeDelta(Node, delta)
   else:
      AddMissingFeature('Variable font size')

def SetNodeNotes(notes):
   if NodeNotesSupported:
      app.SetNodeNotes(Node, notes)
   else:
      AddMissingFeature('Node Notes')    

def MarkAsPureImage():
   if PictureSupported and morphTarget == None:
      Node.IsPureImage = True

def RegisterImage(name, image):
   if PictureSupported:
      app.RegisterImage(name, image)

def StringToImage(guid, imageAsString):
   return app.StringToImage(guid, imageAsString)

def Morph(node, flavor):
   global Node
   app.Morph(node, flavor)
   Node = node

def GetFernViews():
   global Views
   global View
   global Loading
   Views = [v for v in app.Views if v.TypeName == 'FernView']
   Loading = len(Views) == 0
   if Loading:
      View = app.Gui.CreateView('FernView')
      Views = [ View ]
   else:
      View = app.CurrentView

def SetLocation(node, x, y):
    for v in Views:
       v.SetLocation(node, x, y)

def SetOffset(offsetFrom, node, x, y):
   if Loading:
      View.SetOffset(offsetFrom, node, x, y)
   else:
      for v in Views:
         v.SetOffset(offsetFrom, node, x, y)

def SetSize(node, width, height):
   if Loading:
      View.SetSize(node, width, height)
   else:
      for v in Views:
         v.SetSize(node, width, height)

def ShowPrintPagePreview(show):
   if Loading:
      View.ShowPrintPagePreview(show, False)

def SetBackgroundImage(image):
   if Loading:
      View.SetBackgroundImage(image, False)

def SetOpacity(opacity):
   if Loading:
      View.SetOpacity(opacity)

def SetSelection(node):
   View.SetSelection(node)

def SetScratchpadText(text):
   if ScratchpadTextSupported:
      app.Gui.SetScratchpadText(text)
   else:
      AddMissingFeature('saved scratchpad')

def SetImageWidth(node, width):
   if PictureSupported:
      for v in Views:
         v.SetImageWidth(node, width)

def SetShowOutline(node, show):
   if ShowOutlineSupported:
      for v in Views:
         v.SetShowOutline(node, show)

def SetLockHeading(node, lockHeading):
   if LockHeadingSupported:
      for v in Views:
         v.SetLockHeading(node, lockHeading)

def SetOrientation(node, orientation):
  if TwoOrientationsSupported and (orientation == 'LeftToRight'):
      for v in Views:
         v.SetOrientation(node, orientation)
  else:
      AddMissingFeature(orientation + ' layout')
      
def HideChildren(node):
   if HideChildrenSupported:
      for v in Views:
         v.HideChildren(node)
   else:
      AddMissingFeature('Hidden children')

def Minimize(node):
   if MinimizeNodeSupported:
      for v in Views:
         v.Minimize(node)
   else:
      AddMissingFeature('Minimized node')

def SetNodeHorizontalSeparation(node, value):
   if SeparationSupported:
      for v in Views:
         v.SetNodeHorizontalSeparation(node, value)
   else:
      AddMissingFeature('Sibling separation')

def SetVerticalSeparation(node, value):
   if SeparationSupported:
      for v in Views:
         v.SetVerticalSeparation(node, value)
   else:
      AddMissingFeature('Parent-child separation')

def SetBranchHorizontalSeparation(node, value):
   if SeparationSupported:
      for v in Views:
         v.SetBranchHorizontalSeparation(node, value)
   else:
      AddMissingFeature('Branch separation')

def SetEssayType(node, type):
   if EssayTypeSupported:
      app.SetEssayType(node, type)
   else:
      AddMissingFeature('Essay Type')

def JumbleMap(node):
   if JumbleMapSupported:
      for v in Views:
         v.JumbleMap(node)
   else:
      AddMissingFeature('Map Jumble')

missing = {}

def AddMissingFeature(feature):
   if feature not in missing:
      i = 1
   else:
      i = missing[feature] + 1
   missing[feature] = i

def MissingFeaturesList():
   list = ''
   for k in missing.keys():
      n = missing[k]

      list = list + '    ' + str(n) + ' ' + k
      if n != 1:
          list = list + 's'

      list = list + CR()
   return list 
   
def GenerateMissingFeaturesReport():
   if len(missing) > 0:
      Create('Note')
      SetSize(Node, 350, 100)
      SetText( MissingFeaturesHeader() + CR() \
               + MissingFeaturesList() + CR() \
               + MissingFeaturesFooter())

def MissingFeaturesHeader():
   return '*' + 'Warning: This file was created with a more recent version of Rationale' + '*' + CR() \
              + 'Unable to load:'

def MissingFeaturesFooter():
   return 'You can upgrade to the latest version of Rationale at http://rationale.austhink.com/r/download'

def CR():
    return '\r\n'
#-- End of Prelude --

<?php echo $template_body; ?>

GetFernViews()

SetLocation(map0_0, 63, -182)

SetSelection(map0_0)

ShowPrintPagePreview(False)

GenerateMissingFeaturesReport()
